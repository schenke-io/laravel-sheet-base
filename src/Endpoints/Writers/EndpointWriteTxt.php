<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriter;

class EndpointWriteTxt extends StorageFileWriter
{
    protected string $extension = 'txt';

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $content = implode("\n", array_keys($pipelineData->toArray()));

        $this->storagePut($this->path, $content);
    }
}
