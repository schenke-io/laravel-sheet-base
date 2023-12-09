<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

final class SheetBaseConfig
{
    public const CONFIG_FILE_BASE = 'sheet-base';

    /** @var array<string,Pipeline> */
    public array $pipelines = [];

    /**
     * @throws ConfigErrorException
     */
    public static function make(): SheetBaseConfig
    {
        $configProject = new SheetBaseConfig();
        $pipelines = [];
        foreach ($configProject->getConfig() as $pipelineName => $pipeline) {
            $pipelines[$pipelineName] = Pipeline::fromConfig($pipeline, $pipelineName);
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
        } catch (ConfigErrorException|ReadParseException|\Exception $e) {
            return $e->getMessage();
        }

        return null;
    }

    protected function getConfig(): array
    {
        return config(self::CONFIG_FILE_BASE.'.pipelines');
    }
}
