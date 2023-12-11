<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints;

use SchenkeIo\LaravelSheetBase\EndpointBases\DataReader;

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
