<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadCsv;

class DummyEndpointReadCsv extends EndpointReadCsv
{
    public string $path = '/test.csv';
}
