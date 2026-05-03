<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;

/**
 * Class DataReader
 *
 * Abstract base class for endpoints that read data into the pipeline.
 *
 * Main Responsibilities:
 * - Data Retrieval: Provides an interface for fetching data as an array of rows.
 * - Pipeline Integration: Implements the logic to populate a PipelineData object with retrieved data.
 * - String Representation: Returns a human-readable identifier for the reader.
 *
 * Usage Example:
 * ```php
 * class MyDataReader extends DataReader {
 *     public function getArray(): array { return [['id' => 1, 'name' => 'test']]; }
 *     public function explain(): string { return 'reads from my source'; }
 * }
 * ```
 */
abstract class DataReader implements IsReader
{
    /**
     * get an array of rows to be filled into the pipeline
     *
     * @return array<int, array<string, mixed>>
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
