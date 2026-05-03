<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Nette\Schema\ValidationException;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFileReader
 *
 * Base class for reading YAML files into the pipeline.
 *
 * Main Responsibilities:
 * - YAML Parsing: Uses Symfony Yaml component to parse file content.
 * - Error Handling: Catches and rethrows parsing exceptions as EndpointCodeExceptions.
 * - Data Integration: Iterates through parsed YAML data and adds rows to the pipeline.
 *
 * Usage Example:
 * ```php
 * class MyYamlReader extends YamlFileReader {
 *     protected string $extension = 'yaml';
 * }
 * ```
 */
class YamlFileReader extends StorageFileReader
{
    protected string $extension = '';

    /**
     * get data and fill it into the pipeline
     *
     * @throws EndpointCodeException
     * @throws ParseException
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        try {
            $fileContent = (string) $this->storageGet($this->path);
            $yaml = Yaml::parse($fileContent);
        } catch (ValidationException|ParseException $e) {
            throw new EndpointCodeException(class_basename($this), $e->getMessage());
        }
        if (! is_array($yaml)) {
            throw new EndpointCodeException(class_basename($this), 'file did not result in an array');
        }
        foreach ($yaml as $row) {
            /** @var array<string, mixed> $rowArray */
            $rowArray = (array) $row;
            $pipelineData->addRow($rowArray);
        }
    }
}
