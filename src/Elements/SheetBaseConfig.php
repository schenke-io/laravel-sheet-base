<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Exception;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;

final class SheetBaseConfig
{
    public const CONFIG_FILE_BASE = 'sheet-base';

    /** @var array<string,Pipeline> */
    public array $pipelines = [];

    /**
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     */
    public static function make(): SheetBaseConfig
    {
        $configProject = new SheetBaseConfig();
        $pipelines = [];
        foreach ($configProject->getConfig() as $pipelineName => $pipeline) {
            $pipeline = Pipeline::fromConfig($pipeline, $pipelineName);

            $pipelines[$pipelineName] = $pipeline;
        }
        $configProject->pipelines = $pipelines;

        return $configProject;
    }

    /**
     * returns error or null when OK
     */
    public static function checkAndReportError(): ?string
    {
        try {
            SheetBaseConfig::make();
        } catch (ConfigErrorException|SchemaVerifyColumnsException|Exception $e) {
            return $e->getMessage();
        }

        return null;
    }

    protected function getConfig(): array
    {
        return config(self::CONFIG_FILE_BASE.'.pipelines') ?? [];
    }
}
