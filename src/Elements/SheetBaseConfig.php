<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Exception;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;

final class SheetBaseConfig
{
    public const CONFIG_FILE_BASE = 'sheet-base';

    /** @var array<string,Pipeline> */
    public array $pipelines = [];

    /**
     * @throws \Throwable
     * @throws FileSystemNotDefinedException
     * @throws EndpointCodeException
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     * @throws MakeEndpointException
     */
    public static function make(): SheetBaseConfig
    {
        $configProject = new SheetBaseConfig();
        $pipelines = [];
        $targets = [];
        $languagePipelineCount = 0;
        foreach ($configProject->getConfig() as $pipelineName => $pipelineArray) {
            $target = $pipelineArray['target'];

            if (in_array($target, $targets)) {
                throw new ConfigErrorException($pipelineName, 'multiple use of target: '.$target);
            } else {
                $targets[] = $target;
            }
            $pipeline = Pipeline::fromConfig($pipelineArray, $pipelineName);
            if ($pipeline->isLanguage()) {
                $languagePipelineCount++;
                throw_if(
                    $languagePipelineCount > 1,
                    new ConfigErrorException($pipelineName, 'multiple definition of a language pipeline')
                );
            }
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
