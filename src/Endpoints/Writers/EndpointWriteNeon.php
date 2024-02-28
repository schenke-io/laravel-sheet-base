<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use Nette\Neon\Neon;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriter;
use SchenkeIo\LaravelSheetBase\Skills\Comments;

class EndpointWriteNeon extends StorageFileWriter
{
    use Comments;

    protected string $extension = 'neon';

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $content = $this->getComment('#', $writingClass);
        $content .= Neon::encode($pipelineData->toArray(), true);
        $this->storagePut($this->path, $content);
    }
}
