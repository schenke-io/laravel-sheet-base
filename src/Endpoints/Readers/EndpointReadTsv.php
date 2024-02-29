<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReadExcel;

class EndpointReadTsv extends StorageFileReadExcel
{
    protected string $extension = 'tsv';

    protected string $separator = "\t";
}
