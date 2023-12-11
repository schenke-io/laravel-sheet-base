<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Closure;
use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
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
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     */
    public static function fromConfig(array $pipeline, string $pipelineName): Pipeline
    {
        return new Pipeline(
            self::getSources($pipeline['sources'] ?? [], $pipelineName),
            self::getSchema($pipeline['schema'] ?? '', $pipelineName),
            self::getTarget($pipeline['target'] ?? '', $pipelineName)
        );
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
     */
    protected static function getSources(mixed $sources, string $pipelineName): array
    {
        if (! is_array($sources)) {
            throw new ConfigErrorException($pipelineName, 'sources must be defined as array');
        }
        if (count($sources) == 0) {
            throw new ConfigErrorException($pipelineName, 'no sources found');
        }
        $return = [];
        foreach ($sources as $source) {
            if (class_exists($source)) {
                if (! in_array(IsReader::class, class_implements($source))) {
                    throw new ConfigErrorException($pipelineName, "is not a valid source class (IsReader): $source");
                }
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
     */
    protected static function getSchema(string $schema, string $pipelineName): SheetBaseSchema
    {
        if (! class_exists($schema)) {
            throw new ConfigErrorException($pipelineName, "schema class does not exist: $schema");
        }
        if (! in_array(SheetBaseSchema::class, class_parents($schema))) {
            throw new ConfigErrorException($pipelineName, "schema not valid: $schema");
        }
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
     */
    protected static function getTarget(string $target, string $pipelineName): IsWriter
    {

        if (class_exists($target)) {
            if (! in_array(IsWriter::class, class_implements($target))) {
                throw new ConfigErrorException($pipelineName, "is not a valid target class (IsWriter): $target");
            }

            return new $target();
        } else {
            // try filename for auto
            return MakeEndpoint::fromTarget($target);
        }
    }
}
