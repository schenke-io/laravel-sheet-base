<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;

abstract class DataReader implements IsReader
{
    /**
     * get an array of rows to be filled into the pipeline
     *
     * @return array<int,array>
     */
    abstract public function getArray(): array;

    /**
     * get data and fill it into the pipeline
     */
    final public function fillPipeline(PipelineData &$pipelineData): void
    {
        foreach ($this->getArray() as $row) {
            $pipelineData->addRow($row);
        }
    }

    public function toString(): string
    {
        return class_basename($this);
    }
}
