<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriteCsv;

class EndpointWriteCsv extends StorageFileWriteCsv
{
    protected string $extension = 'csv';

    protected string $delimiter = ',';
}
