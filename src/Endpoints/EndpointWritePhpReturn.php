<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints;

use SchenkeIo\LaravelSheetBase\Elements\EndpointBases\StorageFileWriter;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Skills\Comments;
use SchenkeIo\LaravelSheetBase\Skills\MakePhp;

class EndpointWritePhpReturn extends StorageFileWriter
{
    use Comments;
    use MakePhp;

    protected string $extension = 'php';

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $this->storagePut(
            $this->path,
            $this->getPhp($pipelineData->toArray(), $writingClass)
        );
    }
}
