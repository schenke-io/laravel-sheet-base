<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReader;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Skills\ReadExcel;

class EndpointReadPsv extends StorageFileReader
{
    use ReadExcel;

    protected string $extension = 'psv';

    /**
     * get data and fill it into the pipeline
     *
     * @throws ReadParseException
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        foreach ($this->getCsvLines(
            content: $this->storageGet($this->path),
            separator: '|',
            sheetBaseSchema: $pipelineData->sheetBaseSchema
        ) as $row) {
            $pipelineData->addRow($row);
        }
    }
}
