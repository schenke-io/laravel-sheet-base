<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Google\Service\Exception as GoogleException;
use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadGoogleSheet;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteLang;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;
use SchenkeIo\LaravelSheetBase\Google\GoogleBackgroundPainter;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;

final readonly class Pipeline
{
    public bool $isLanguage;

    /**
     * @param  IsReader[]  $sources
     * @param  IsWriter  $target
     *
     * @throws ConfigErrorException
     */
    public function __construct(
        public string $name,
        public array $sources,
        public SheetBaseSchema $schema,
        public IsEndpoint $target,
        public ?IsReader $filter,
        public bool $sync

    ) {
        $sourceCount = count($this->sources);
        $this->isLanguage = $this->target instanceof EndpointWriteLang;
        if ($this->isLanguage) {
            if ($this->sync) {
                if (! $this->sources[0] instanceof EndpointReadGoogleSheet) {
                    throw ConfigErrorException::syncRequiresGoogleSheet($this->name);
                }
                if ($sourceCount > 1) {
                    throw ConfigErrorException::syncRequiresSingleSource($this->name, $sourceCount);
                }
                if (is_null($this->filter)) {
                    throw ConfigErrorException::syncRequiresFilter($this->name);
                }
            }
        } elseif ($this->sync) {
            throw ConfigErrorException::syncImpossibleInNonLanguagePipeline($this->name);
        }

    }

    /**
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     * @throws SchemaVerifyColumnsException
     */
    public static function fromConfig(array $pipeline, string $pipelineName): Pipeline
    {
        $knownKeys = explode(',', 'sources,schema,target,filter,sync');
        $foundKeys = array_keys($pipeline);
        $unknownKeys = array_diff($foundKeys, $knownKeys);
        if (count($unknownKeys) > 0) {
            throw ConfigErrorException::unknownKeysInConfig($pipelineName, $unknownKeys);
        }
        if (! is_array($pipeline['sources'])) {
            throw ConfigErrorException::sourceNotConfiguredAsArray($pipelineName);
        }

        return new Pipeline(
            name: $pipelineName,
            sources: self::getSources($pipeline['sources'], $pipelineName),
            schema: self::getSchema($pipeline['schema'] ?? '', $pipelineName),
            target: self::getTarget($pipeline['target'] ?? '', $pipelineName),
            filter: self::getFilter($pipeline['filter'] ?? null, $pipelineName),
            sync: $pipeline['sync'] ?? false
        );

    }

    /**
     * -----------------------------------------------------------------------------------
     *
     *                               main pump functionality
     *
     * -----------------------------------------------------------------------------------
     *
     * @throws EndpointCodeException
     * @throws GoogleException
     */
    public function pump(Command $cmd, string $namePipeline, string $writingClassName): void
    {
        $cmd->info("|----------   pipeline '$namePipeline' with schema: ".class_basename($this->schema));
        $pipelineData = new PipelineData($this->schema);
        /*
         * read all sources
         */
        foreach ($this->sources as $source) {
            $source->fillPipeline($pipelineData);
            $cmd->info(sprintf("pipeline '%s' source %s %s",
                $namePipeline, class_basename($source), $source->explain()
            ));
        }
        /*
         * filter the ids
         */
        $keysToRemove = $pipelineData->filterKeysOff($cmd, $namePipeline, $this->filter);
        /*
         * write the data to the target
         */
        $this->target->releasePipeline($pipelineData, $writingClassName);
        /*
         * do the reverse sync
         */
        if ($this->sync) {
            GoogleBackgroundPainter::take($cmd, $namePipeline, $this->sources[0])->markRed($keysToRemove);
        }

        $cmd->info(sprintf("pipeline '%s' target %s  %s",
            $namePipeline, class_basename($this->target), $this->target->explain()
        ));
    }

    /**
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     */
    protected static function getFilter(?string $filter, string $pipelineName): ?IsReader
    {
        if (is_null($filter)) {
            // empty endpoint, no filtering
            return null;
        }
        if (class_exists($filter)) {
            if (! in_array(IsReader::class, class_implements($filter))) {
                throw ConfigErrorException::filterClassIsNotReader($pipelineName, $filter);
            }

            return new $filter;
        } else {
            return FindEndpointClass::fromSource($filter);
        }
    }

    /**
     * @param  string[]|IsReader[]  $sources
     * @return array<int,IsReader>
     *
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     */
    protected static function getSources(array $sources, string $pipelineName): array
    {
        if (count($sources) == 0) {
            throw ConfigErrorException::noSourcesDefined($pipelineName);
        }

        $return = [];
        foreach ($sources as $source) {
            if ($source instanceof IsReader) {
                $return[] = $source;
            } elseif (class_exists($source)) {
                if (! in_array(IsReader::class, class_implements($source))) {
                    throw ConfigErrorException::invalidSource($pipelineName, $source);
                }

                $return[] = new $source;
            } else {
                // try filename for auto
                $return[] = FindEndpointClass::fromSource($source);
            }
        }

        return $return;
    }

    /**
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     */
    protected static function getSchema(string $schema, string $pipelineName): SheetBaseSchema
    {
        if (! class_exists($schema)) {
            throw ConfigErrorException::schemaDoesNotExist($pipelineName, $schema);
        }
        if (! in_array(SheetBaseSchema::class, class_parents($schema))) {
            throw ConfigErrorException::schemaInvalid($pipelineName, $schema);
        }
        /** @var SheetBaseSchema $class */
        $class = new $schema;
        $class->verify($pipelineName);

        return new $schema;
    }

    /**
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     */
    protected static function getTarget(string $target, string $pipelineName): IsWriter
    {
        if ($target == '') {
            throw ConfigErrorException::emptyTarget($pipelineName);
        }
        if (class_exists($target)) {
            if (! in_array(IsWriter::class, class_implements($target))) {
                throw ConfigErrorException::invalidTarget($pipelineName, $target);
            }

            return new $target;
        } else {
            // try filename for auto
            return FindEndpointClass::fromTarget($target);
        }
    }
}
