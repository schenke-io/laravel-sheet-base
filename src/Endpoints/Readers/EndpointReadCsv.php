<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReadExcel;

class EndpointReadCsv extends StorageFileReadExcel
{
    protected string $extension = 'csv';

    protected string $separator = ',';
}
