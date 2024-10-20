<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriteCsv;

class EndpointWriteTsv extends StorageFileWriteCsv
{
    protected string $extension = 'tsv';

    protected string $delimiter = "\t";
}
