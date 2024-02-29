<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadPsv;

class DummyEndpointReadPsv extends EndpointReadPsv
{
    public string $path = '/test.psv';
}
