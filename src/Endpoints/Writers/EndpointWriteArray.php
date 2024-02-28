<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;

class EndpointWriteArray implements IsWriter
{
    public array $arrayData = [];

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $this->arrayData = $pipelineData->toArray();
    }

    public function explain(): string
    {
        return 'writes into public array $arrayData';
    }
}
