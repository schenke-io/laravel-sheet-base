<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriter;

class EndpointWriteJson extends StorageFileWriter
{
    protected string $extension = 'json';

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $content = json_encode($pipelineData->toArray(),
            JSON_PRETTY_PRINT +
            JSON_UNESCAPED_SLASHES +
            JSON_UNESCAPED_UNICODE
        );
        $this->storagePut($this->path, $content);
    }
}
