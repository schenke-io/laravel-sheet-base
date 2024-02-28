<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Closure;
use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteLang;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;
use SchenkeIo\LaravelSheetBase\Skills\MakeEndpoint;

final class Pipeline
{
    /**
     * @param  array<int,IsReader>  $sources
     * @param  IsWriter  $target
     */
    public function __construct(
        public readonly array $sources,
        public readonly SheetBaseSchema $schema,
        public readonly IsEndpoint $target

    ) {
    }

    /**
     * @throws ReadParseException
     * @throws FileSystemNotDefinedException
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     * @throws \Throwable
     * @throws SchemaVerifyColumnsException
     */
    public static function fromConfig(array $pipeline, string $pipelineName): Pipeline
    {

        return new Pipeline(
            sources: self::getSources($pipeline['sources'] ?? [], $pipelineName),
            schema: self::getSchema($pipeline['schema'] ?? '', $pipelineName),
            target: self::getTarget($pipeline['target'] ?? '', $pipelineName)
        );

    }

    public function isLanguage(): bool
    {
        return $this->target instanceof EndpointWriteLang;
    }

    /**
     * @throws ReadParseException
     */
    public function pump(Closure $callback, string $name, string $className): void
    {
        $callback("|----------   pipeline $name");
        $pipelineData = new PipelineData($this->schema);
        foreach ($this->sources as $source) {
            $source->fillPipeline($pipelineData);
            $callback(sprintf("pipeline '%s' source %s %s",
                $name, get_class($source), $source->explain()
            ));
        }
        $this->target->releasePipeline($pipelineData, $className);
        $callback(sprintf("pipeline '%s'target %s  %s",
            $name, get_class($this->target), $this->target->explain()
        ));
    }

    /**
     * @param  array<int,string>  $sources
     * @return array<int,IsReader>
     *
     * @throws ConfigErrorException
     * @throws FileSystemNotDefinedException
     * @throws MakeEndpointException
     * @throws ReadParseException
     * @throws \Throwable
     */
    protected static function getSources(mixed $sources, string $pipelineName): array
    {
        throw_unless(is_array($sources), new ConfigErrorException($pipelineName, 'sources must be defined as array'));
        throw_if(count($sources) == 0, new ConfigErrorException($pipelineName, 'no sources found'));

        $return = [];
        foreach ($sources as $source) {
            if (class_exists($source)) {
                throw_unless(
                    in_array(IsReader::class, class_implements($source)),
                    new ConfigErrorException($pipelineName, "is not a valid source class (IsReader): $source")
                );

                $return[] = new $source();
            } else {
                // try filename for auto
                $return[] = MakeEndpoint::fromSource($source);
            }
        }

        return $return;
    }

    /**
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     * @throws \Throwable
     */
    protected static function getSchema(string $schema, string $pipelineName): SheetBaseSchema
    {
        throw_unless(
            class_exists($schema),
            new ConfigErrorException($pipelineName, "schema class does not exist: $schema")
        );
        throw_unless(
            in_array(SheetBaseSchema::class, class_parents($schema)),
            new ConfigErrorException($pipelineName, "schema not valid: $schema")
        );
        /** @var SheetBaseSchema $class */
        $class = new $schema();
        $class->verify($pipelineName);

        return new $schema();
    }

    /**
     * @throws ConfigErrorException
     * @throws FileSystemNotDefinedException
     * @throws MakeEndpointException
     * @throws ReadParseException
     * @throws \Throwable
     */
    protected static function getTarget(string $target, string $pipelineName): IsWriter
    {
        throw_if($target == '', new ConfigErrorException($pipelineName, 'target is empty'));
        if (class_exists($target)) {
            throw_unless(
                in_array(IsWriter::class, class_implements($target)),
                new ConfigErrorException($pipelineName, "is not a valid target class (IsWriter): $target")
            );

            return new $target();
        } else {
            // try filename for auto
            return MakeEndpoint::fromTarget($target);
        }
    }
}
