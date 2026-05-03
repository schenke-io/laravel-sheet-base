<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\EndpointBases\DelimitedFileWriter;

class EndpointWriteCsv extends DelimitedFileWriter
{
    protected string $extension = 'csv';

    protected string $delimiter = ',';
}
