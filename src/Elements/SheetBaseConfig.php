<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Exception;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;

final class SheetBaseConfig
{
    public const CONFIG_FILE_BASE = 'sheet-base';

    /** @var array<string,Pipeline> */
    public array $pipelines = [];

    /**
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     * @throws MakeEndpointException
     */
    public static function make(): SheetBaseConfig
    {
        $configProject = new SheetBaseConfig;
        $targets = [];
        $pipelineIsLanguageCount = 0;
        $pipelines = $configProject->getConfig();
        foreach ($pipelines as $pipelineName => $pipelineArray) {
            /*
             * we check the pipeline during its construction
             */
            $pipeline = Pipeline::fromConfig($pipelineArray, $pipelineName);
            /*
             * each target only to be used once
             */
            $target = $pipeline->target->toString();
            if (in_array($target, $targets)) {
                throw ConfigErrorException::targetAlreadyUsed($pipelineName, $target);
            }
            $targets[] = $target;
            /*
             * maximum one language pipe
             */
            if ($pipeline->isLanguage) {
                $pipelineIsLanguageCount++;
                if ($pipelineIsLanguageCount > 1) {
                    //dd($pipelineIsLanguageCount);
                    throw ConfigErrorException::languagePipelineDefinedTwice($pipelineName);
                }
            }

            $configProject->pipelines[$pipelineName] = $pipeline;
        }

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
