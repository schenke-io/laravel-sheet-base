<?php

namespace SchenkeIo\LaravelSheetBase\Contracts;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;

/**
 * Interface for endpoint writers.
 */
interface IsWriter extends IsEndpoint
{
    /**
     * Release pipeline data using the specified writing class.
     */
    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void;
}
