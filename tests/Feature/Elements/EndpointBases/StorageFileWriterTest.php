<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\EndpointBases\StorageFileWriter;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class StorageFileWriterTest extends ConfigTestCase
{
    public function testStoragePut(): void
    {
        $path = '/testfile.txt';
        $content = 'some random text';
        Storage::fake('sheet-base');

        $file = new class extends StorageFileWriter
        {
            public string $path = '/testfile.txt';

            protected string $extension = 'txt';

            public function publicStoragePut(string $path, string $content): void
            {
                $this->storagePut($path, $content);
            }

            public function releasePipeline(PipelineData $pipelineData, string $writingClass)
            {
                // TODO: Implement releasePipeline() method.
            }
        };
        $file->publicStoragePut($path, $content);
        $this->assertEquals($content, Storage::disk('sheet-base')->get($path));
    }
}
