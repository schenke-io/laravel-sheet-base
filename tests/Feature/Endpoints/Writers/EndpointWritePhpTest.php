<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Writers;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWritePhp;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointWritePhpTest extends ConfigTestCase
{
    public function testWritePhp()
    {
        $path = '/test.php';
        //dd(Storage::getConfig());
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
        $php = new class extends EndpointWritePhp
        {
            public string $path = '/test.php';
        };
        Storage::disk('sheet-base')->assertMissing($path);
        $php->releasePipeline($tempTable, 'x');
        Storage::disk('sheet-base')->assertExists($path);
    }
}
