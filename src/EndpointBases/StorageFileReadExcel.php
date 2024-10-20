<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Skills\ReadExcel;

class StorageFileReadExcel extends StorageFileReader
{
    use ReadExcel;

    protected string $extension = '';

    protected string $separator = '';

    public function __construct(?string $path = null)
    {
        parent::__construct($path);
        $classname = class_basename($this);
        if ($this->separator == '') {
            throw new EndpointCodeException($classname, '$separator is not defined');
        }
    }

    /**
     * get data and fill it into the pipeline
     *
     * @throws DataReadException
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        foreach ($this->getCsvLines(
            content: $this->storageGet($this->path),
            separator: $this->separator,
            sheetBaseSchema: $pipelineData->sheetBaseSchema
        ) as $row) {
            $pipelineData->addRow($row);
        }
    }
}
