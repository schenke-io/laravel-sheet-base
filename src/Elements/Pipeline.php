<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Closure;
use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;

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
     */
    public static function fromConfig(array $pipeline, string $pipelineName): Pipeline
    {
        return new Pipeline(
            self::getSources($pipeline['sources'] ?? [], $pipelineName),
            self::getSchema($pipeline['schema'] ?? '', $pipelineName),
            self::getTarget($pipeline['target'] ?? '', $pipelineName)
        );
    }

    public function pump(Closure $callback, string $name, string $className): void
    {
        $callback("pipeline $name");
        $pipelineData = new PipelineData($this->schema);
        foreach ($this->sources as $source) {
            $source->fillPipeline($pipelineData);
            $callback(sprintf("pipeline '%s' filled with source %s",
                $name, get_class($source)
            ));
        }
        $this->target->releasePipeline($pipelineData, $className);
        $callback(sprintf("pipeline '%s' released to target %s",
            $name, get_class($this->target)
        ));
    }

    /**
     * @param  array<int,string>  $sources
     * @return array<int,IsReader>
     *
     * @throws ConfigErrorException
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
            if (! class_exists($source)) {
                throw new ConfigErrorException($pipelineName, "source class does not exist: $source");
            }
            if (! in_array(IsReader::class, class_implements($source))) {
                throw new ConfigErrorException($pipelineName, "is not a valid source class (IsReader): $source");
            }
            $return[] = new $source();
        }

        return $return;
    }

    /**
     * @throws ConfigErrorException
     */
    protected static function getSchema(string $schema, string $pipelineName): SheetBaseSchema
    {
        if (! class_exists($schema)) {
            throw new ConfigErrorException($pipelineName, "schema class does not exist: $schema");
        }
        if (! in_array(SheetBaseSchema::class, class_parents($schema))) {
            throw new ConfigErrorException($pipelineName, "schema not valid: $schema");
        }

        return new $schema();
    }

    /**
     * @throws ConfigErrorException
     */
    protected static function getTarget(string $target, string $pipelineName): IsWriter
    {
        if (! class_exists($target)) {
            throw new ConfigErrorException($pipelineName, "target class does not exist: $target");
        }
        if (! in_array(IsWriter::class, class_implements($target))) {
            throw new ConfigErrorException($pipelineName, "is not a valid target class (IsWriter): $target");
        }

        return new $target();

    }
}
