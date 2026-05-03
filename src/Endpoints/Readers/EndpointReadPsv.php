<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\DelimitedFileReader;

class EndpointReadPsv extends DelimitedFileReader
{
    protected string $extension = 'psv';

    protected string $delimiter = '|';
}
