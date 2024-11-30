<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

use Exception;

class ConfigErrorException extends Exception
{
    public function __construct(string $pipelineName, string $msg, int $code = 0)
    {
        parent::__construct(sprintf("in pipeline '%s': %s | %d", $pipelineName, $msg, $code));
    }

    public static function filterClassIsNotReader(string $pipelineName, string $filter): ConfigErrorException
    {
        return new self($pipelineName, "filter is not read endpoint ($filter)", 1);
    }

    public static function schemaDoesNotExist(string $pipelineName, string $schema): ConfigErrorException
    {
        return new self($pipelineName, "schema does not exist ($schema)", 2);
    }

    public static function schemaInvalid(string $pipelineName, string $schema): ConfigErrorException
    {
        return new self($pipelineName, "schema is not valid ($schema)", 3);
    }

    public static function sourceNotConfiguredAsArray(string $pipelineName): ConfigErrorException
    {
        return new self($pipelineName, 'source is not defined as array', 4);
    }

    public static function noSourcesDefined(string $pipelineName): ConfigErrorException
    {
        return new self($pipelineName, 'no sources defined', 5);
    }

    public static function invalidSource(string $pipelineName, string $source): ConfigErrorException
    {
        return new self($pipelineName, "invalid source ($source)", 6);
    }

    public static function emptyTarget(string $pipelineName): ConfigErrorException
    {
        return new self($pipelineName, 'empty target', 7);
    }

    public static function invalidTarget(string $pipelineName, string $target): ConfigErrorException
    {
        return new self($pipelineName, "invalid target ($target)", 8);
    }

    public static function targetAlreadyUsed(string $pipelineName, mixed $target): ConfigErrorException
    {
        return new self($pipelineName, "target already used ($target)", 9);
    }

    public static function languagePipelineDefinedTwice(string $pipelineName): ConfigErrorException
    {
        return new self($pipelineName, 'language pipeline already defined', 10);
    }

    public static function syncRequiresGoogleSheet(string $pipelineName): ConfigErrorException
    {
        return new self($pipelineName, 'sync requires Google Sheet', 11);
    }

    public static function syncRequiresSingleSource(string $pipelineName, int $count): ConfigErrorException
    {
        return new self($pipelineName, "sync requires single source but found $count", 12);
    }

    public static function syncImpossibleInNonLanguagePipeline(string $pipelineName): ConfigErrorException
    {
        return new self($pipelineName, 'sync impossible in non language pipeline', 13);
    }

    public static function syncRequiresFilter(string $name): ConfigErrorException
    {
        return new self($name, 'sync requires filter', 14);
    }

    public static function unknownKeysInConfig(string $pipelineName, array $unknownKeys): ConfigErrorException
    {
        return new self($pipelineName,
            sprintf('unknown keys in config (%s)',
                implode(', ', $unknownKeys)
            ), 15);
    }
}
