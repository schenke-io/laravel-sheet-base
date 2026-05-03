<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\EndpointBases\DelimitedFileWriter;

class EndpointWriteTsv extends DelimitedFileWriter
{
    protected string $extension = 'tsv';

    protected string $delimiter = "\t";
}
