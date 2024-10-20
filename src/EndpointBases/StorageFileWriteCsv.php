<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\DataQualityException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

class StorageFileWriteCsv extends StorageFileWriter
{
    /**
     * needs to be overwritten
     */
    protected string $delimiter = '';

    public function __construct(?string $path = null)
    {
        parent::__construct($path);
        if ($this->delimiter == '') {
            throw new EndpointCodeException(class_basename($this), 'delimiter cannot be empty');
        }
    }

    /**
     * @throws DataQualityException
     */
    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $headers = array_keys($pipelineData->sheetBaseSchema->getColumns());
        $content = implode($this->delimiter, $headers)."\n";
        foreach ($pipelineData->toArray() as $index => $row) {
            array_unshift($row, $index);
            // we exit when the data includes the delimiter
            foreach ($row as $cell) {
                if (str_contains($cell, $this->delimiter)) {
                    throw new DataQualityException("the processed data included the delimiter: $cell");
                }
            }
            $content .= implode($this->delimiter, $row)."\n";
        }
        $this->storagePut($this->path, $content);
    }
}
