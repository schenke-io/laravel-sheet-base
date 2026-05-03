<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\EndpointBases\DelimitedFileWriter;

class EndpointWritePsv extends DelimitedFileWriter
{
    protected string $extension = 'psv';

    protected string $delimiter = '|';
}
