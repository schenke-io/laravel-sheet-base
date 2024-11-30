<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadTxt;

class DummyEndpointReadTxt extends EndpointReadTxt
{
    public string $path = '/test.txt';
}
