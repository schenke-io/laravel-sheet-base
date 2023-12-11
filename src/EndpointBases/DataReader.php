<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

abstract class DataReader implements IsReader
{
    /**
     * get an array of rows to be filled into the pipeline
     */
    abstract public function getArray(): array;

    /**
     * get data and fill it into the pipeline
     *
     * @throws ReadParseException
     */
    final public function fillPipeline(PipelineData &$pipelineData): void
    {
        foreach ($this->getArray() as $row) {
            $pipelineData->addRow($row);
        }
    }
}
