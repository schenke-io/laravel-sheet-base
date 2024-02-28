<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadArray;

class EndpointReadArrayTest extends TestCase
{
    public function testGetArray()
    {
        $endpoint = new EndpointReadArray();
        $this->assertTrue(is_array($endpoint->getArray()));
    }
}
