<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Endpoints\EndpointWriteArray;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummySchema;

class EndpointWriteArrayTest extends ConfigTestCase
{
    public function testReleasePipeline(): void
    {
        $pipeline = new PipelineData(new DummySchema());

        $endpoint = new class extends EndpointWriteArray
        {
        };
        $endpoint->releasePipeline($pipeline, '');
        $this->assertEquals([], $endpoint->arrayData);

    }
}
