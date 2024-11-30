<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadYaml;

class DummyEndpointReadYaml extends EndpointReadYaml
{
    public string $path = '/test.yaml';
}
