<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\DataReader;

/**
 * this class is itself an empty class doing nothing
 * it is made to be extended or used as an empty reader
 */
class EndpointReadArray extends DataReader
{
    /**
     * get an array of rows to be filled into the pipeline
     */
    public function getArray(): array
    {
        // this needs to be overwritten
        return [];
    }

    public function explain(): string
    {
        return 'reads from array';
    }
}
