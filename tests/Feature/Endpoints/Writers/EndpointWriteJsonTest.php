<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Writers;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteJson;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointWriteJsonTest extends ConfigTestCase
{
    public function test_release_pipeline(): void
    {
        $path = '/test.json';
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addId('a');
                $this->addUnsigned('b');
            }
        };
        Storage::fake('sheet-base');
        $data = [1 => ['b' => 2], 2 => ['b' => 3]];
        $tempTable = new PipelineData($schema);
        $tempTable->addRow(['a' => 1, 'b' => 2]);
        $tempTable->addRow(['a' => 2, 'b' => 3]);
        $neon = new class extends EndpointWriteJson
        {
            public string $path = '/test.json';
        };
        Storage::disk('sheet-base')->assertMissing($path);
        $neon->releasePipeline($tempTable, 'x');
        Storage::disk('sheet-base')->assertExists($path);
    }
}
