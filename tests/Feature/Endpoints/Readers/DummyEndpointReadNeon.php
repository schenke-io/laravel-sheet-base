<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadNeon;

class DummyEndpointReadNeon extends EndpointReadNeon
{
    public string $path = '/test.neon';
}
