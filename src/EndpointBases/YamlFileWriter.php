<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Skills\Comments;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFileWriter
 *
 * Base class for writing data to YAML files from the pipeline.
 *
 * Main Responsibilities:
 * - YAML Serialization: Uses Symfony Yaml component to convert pipeline data to YAML format.
 * - Comment Injection: Adds header comments to the generated YAML file.
 * - Pipeline Release: Dumps the data and saves it to the specified storage path.
 *
 * Usage Example:
 * ```php
 * class MyYamlWriter extends YamlFileWriter {
 *     protected string $extension = 'yaml';
 * }
 * ```
 */
class YamlFileWriter extends StorageFileWriter
{
    use Comments;

    protected string $extension = '';

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $content = $this->getComment('#', $writingClass);
        $content .= Yaml::dump($pipelineData->toArray());
        $this->storagePut($this->path, $content);
    }
}
