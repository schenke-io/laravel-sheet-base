<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadArray;

class EndpointReadArrayTest extends TestCase
{
    public function test_get_array()
    {
        $endpoint = new EndpointReadArray;
        $this->assertTrue(is_array($endpoint->getArray()));
    }
}
