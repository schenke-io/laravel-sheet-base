<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use Nette\Schema\ValidationException;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReader;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class EndpointReadYaml extends StorageFileReader
{
    protected string $extension = 'yaml';

    /**
     * get data and fill it into the pipeline
     *
     * @throws EndpointCodeException
     * @throws ParseException
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        try {
            $fileContent = $this->storageGet($this->path);
            $yaml = Yaml::parse($fileContent);
        } catch (ValidationException|ParseException $e) {
            throw new EndpointCodeException(class_basename($this), $e->getMessage());
        }
        if (! is_array($yaml)) {
            throw new EndpointCodeException(class_basename($this), 'file did not result in an array');
        }
        foreach ($yaml as $row) {
            $pipelineData->addRow($row);
        }
    }
}
