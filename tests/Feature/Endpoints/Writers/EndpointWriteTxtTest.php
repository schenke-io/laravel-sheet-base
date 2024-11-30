<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Writers;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteTxt;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointWriteTxtTest extends ConfigTestCase
{
    public function test_write_txt()
    {
        $path = '/test.txt';
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addId('a');
            }
        };
        Storage::fake('sheet-base');
        $data = [1 => ['b' => 2], 2 => ['b' => 3]];
        $tempTable = new PipelineData($schema);
        $tempTable->addRow(['a' => 1, 'b' => 2]);
        $tempTable->addRow(['a' => 2, 'b' => 3]);
        $neon = new class extends EndpointWriteTxt
        {
            public string $path = '/test.txt';
        };
        Storage::disk('sheet-base')->assertMissing($path);
        $neon->releasePipeline($tempTable, 'x');
        Storage::disk('sheet-base')->assertExists($path);
    }
}
