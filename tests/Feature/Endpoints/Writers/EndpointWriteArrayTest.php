<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteArray;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummySchema;

class EndpointWriteArrayTest extends ConfigTestCase
{
    public function test_release_pipeline(): void
    {
        $pipeline = new PipelineData(new DummySchema);

        $endpoint = new class extends EndpointWriteArray {};
        $endpoint->releasePipeline($pipeline, '');
        $this->assertEquals([], $endpoint->arrayData);

    }
}
