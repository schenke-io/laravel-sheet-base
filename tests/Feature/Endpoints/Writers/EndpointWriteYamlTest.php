<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Writers;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteYaml;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointWriteYamlTest extends ConfigTestCase
{
    public function test_write_yaml()
    {
        $path = '/test.yaml';
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addId('a');
            }
        };
        Storage::fake('sheet-base');
        $tempTable = new PipelineData($schema);
        $tempTable->addRow(['a' => 1, 'b' => 2]);
        $tempTable->addRow(['a' => 2, 'b' => 3]);
        $neon = new class extends EndpointWriteYaml
        {
            public string $path = '/test.yaml';
        };
        Storage::disk('sheet-base')->assertMissing($path);
        $neon->releasePipeline($tempTable, 'x');
        Storage::disk('sheet-base')->assertExists($path);
    }
}
