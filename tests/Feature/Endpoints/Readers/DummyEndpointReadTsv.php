<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadTsv;

class DummyEndpointReadTsv extends EndpointReadTsv
{
    public string $path = '/test.tsv';
}
