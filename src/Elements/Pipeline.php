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

/**
 * Class Pipeline
 *
 * Manages the flow of data from multiple sources through a schema and filter to a target endpoint.
 *
 * Main Responsibilities:
 * - Data Orchestration: Pumps data from readers to writers.
 * - Validation: Ensures configuration consistency between sources, filters, and targets.
 * - Synchronization: Handles reverse sync for Google Sheets (e.g., marking rows in red).
 *
 * Usage Example:
 * ```php
 * $pipeline = Pipeline::fromConfig($config, 'my-pipeline');
 * $pipeline->pump($command, 'my-pipeline', 'TargetClass');
 * ```
 */
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
     * @param  array<string, mixed>  $pipeline
     *
     * @throws ConfigErrorException
     * @throws MakeEndpointException
     * @throws SchemaVerifyColumnsException
     */
    public static function fromConfig(array $pipeline, string $pipelineName): Pipeline
    {
        return PipelineConfigParser::parse($pipeline, $pipelineName);
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
}
