<?php

namespace SchenkeIo\LaravelSheetBase\Contracts;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;

interface IsReader extends IsEndpoint
{
    /**
     * get data and fill it into the pipeline
     */
    public function fillPipeline(PipelineData &$pipelineData): void;
}
