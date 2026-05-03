<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\DelimitedFileReader;

class EndpointReadCsv extends DelimitedFileReader
{
    protected string $extension = 'csv';

    protected string $delimiter = ',';
}
