<?php

namespace SchenkeIo\LaravelSheetBase\Contracts;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

interface IsReader extends IsEndpoint
{
    /**
     * get data and fill it into the pipeline
     *
     * @throws EndpointCodeException
     */
    public function fillPipeline(PipelineData &$pipelineData): void;
}
