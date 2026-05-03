<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;

class PipelineConfigParser
{
    /**
     * @param  array<string, mixed>  $pipeline
     *
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     * @throws SchemaVerifyColumnsException
     */
    public static function parse(array $pipeline, string $pipelineName): Pipeline
    {
        $knownKeys = explode(',', 'sources,schema,target,filter,sync');
        $foundKeys = array_keys($pipeline);
        $unknownKeys = array_diff($foundKeys, $knownKeys);
        if (count($unknownKeys) > 0) {
            throw ConfigErrorException::unknownKeysInConfig($pipelineName, $unknownKeys);
        }
        $sources = $pipeline['sources'];
        if (! is_array($sources)) {
            throw ConfigErrorException::sourceNotConfiguredAsArray($pipelineName);
        }

        /** @var array<string|IsReader> $sources */
        return new Pipeline(
            name: $pipelineName,
            sources: self::getSources($sources, $pipelineName),
            schema: self::getSchema(is_string($pipeline['schema'] ?? null) ? $pipeline['schema'] : '', $pipelineName),
            target: self::getTarget(is_string($pipeline['target'] ?? null) ? $pipeline['target'] : '', $pipelineName),
            filter: self::getFilter(is_string($pipeline['filter'] ?? null) ? $pipeline['filter'] : null, $pipelineName),
            sync: (bool) ($pipeline['sync'] ?? false)
        );
    }

    /**
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     */
    public static function getFilter(?string $filter, string $pipelineName): ?IsReader
    {
        if (is_null($filter)) {
            // empty endpoint, no filtering
            return null;
        }
        if (class_exists($filter)) {
            if (! in_array(IsReader::class, class_implements($filter))) {
                throw ConfigErrorException::filterClassIsNotReader($pipelineName, $filter);
            }

            /** @var IsReader $instance */
            $instance = new $filter;

            return $instance;
        } else {
            return FindEndpointClass::fromSource($filter);
        }
    }

    /**
     * @param  array<string|IsReader>  $sources
     * @return array<int,IsReader>
     *
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     */
    public static function getSources(array $sources, string $pipelineName): array
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

                /** @var IsReader $instance */
                $instance = new $source;
                $return[] = $instance;
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
    public static function getSchema(string $schema, string $pipelineName): SheetBaseSchema
    {
        if (! class_exists($schema)) {
            throw ConfigErrorException::schemaDoesNotExist($pipelineName, $schema);
        }
        if (! in_array(SheetBaseSchema::class, class_parents($schema))) {
            throw ConfigErrorException::schemaInvalid($pipelineName, $schema);
        }
        /** @var class-string<SheetBaseSchema> $schemaClass */
        $schemaClass = $schema;
        /** @var SheetBaseSchema $instance */
        $instance = new $schemaClass;
        $instance->verify($pipelineName);

        return $instance;
    }

    /**
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     */
    public static function getTarget(string $target, string $pipelineName): IsWriter
    {
        if ($target == '') {
            throw ConfigErrorException::emptyTarget($pipelineName);
        }
        if (class_exists($target)) {
            if (! in_array(IsWriter::class, class_implements($target))) {
                throw ConfigErrorException::invalidTarget($pipelineName, $target);
            }

            /** @var IsWriter $instance */
            $instance = new $target;

            return $instance;
        } else {
            // try filename for auto
            return FindEndpointClass::fromTarget($target);
        }
    }
}
