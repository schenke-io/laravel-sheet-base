<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriter;
use SchenkeIo\LaravelSheetBase\Skills\Comments;
use Symfony\Component\Yaml\Yaml;

class EndpointWriteYaml extends StorageFileWriter
{
    use Comments;

    protected string $extension = 'yaml';

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $content = $this->getComment('#', $writingClass);
        $content .= Yaml::dump($pipelineData->toArray());
        $this->storagePut($this->path, $content);
    }
}
