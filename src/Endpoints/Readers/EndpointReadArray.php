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
     * @return array<int, array<string, mixed>>
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
