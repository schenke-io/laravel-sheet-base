<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\DelimitedFileReader;

class EndpointReadTsv extends DelimitedFileReader
{
    protected string $extension = 'tsv';

    protected string $delimiter = "\t";
}
