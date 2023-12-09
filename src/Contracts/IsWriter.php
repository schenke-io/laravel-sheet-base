<?php

namespace SchenkeIo\LaravelSheetBase\Contracts;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;

interface IsWriter extends IsEndpoint
{
    public function releasePipeline(PipelineData $pipelineData, string $writingClass);
}
