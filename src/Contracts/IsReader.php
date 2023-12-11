<?php

namespace SchenkeIo\LaravelSheetBase\Contracts;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

interface IsReader extends IsEndpoint
{
    /**
     * get data and fill it into the pipeline
     *
     * @throws ReadParseException
     */
    public function fillPipeline(PipelineData &$pipelineData): void;
}
