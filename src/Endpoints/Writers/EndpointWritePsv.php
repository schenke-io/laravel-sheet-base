<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriteCsv;

class EndpointWritePsv extends StorageFileWriteCsv
{
    protected string $extension = 'psv';

    protected string $delimiter = '|';
}
