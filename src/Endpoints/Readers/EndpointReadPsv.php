<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReadExcel;

class EndpointReadPsv extends StorageFileReadExcel
{
    protected string $extension = 'psv';

    protected string $delimiter = '|';
}
