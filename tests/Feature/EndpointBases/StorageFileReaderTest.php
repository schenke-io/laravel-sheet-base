<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;


use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReader;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class StorageFileReaderTest extends ConfigTestCase
{
    public function testFileNotFound()
    {
        Storage::fake('sheet-base');
        $this->expectException(ReadParseException::class);
        $file = new class extends StorageFileReader
        {
            public string $path = '/unknown filename.txt';

            protected string $extension = 'txt';

            /**
             * get data and fill it into the pipeline
             */
            public function fillPipeline(PipelineData &$pipelineData): void
            {
                // TODO: Implement fillPipeline() method.
            }
        };
    }

    public function testStorageGet(): void
    {
        $path = '/testfile.txt';
        $content = 'some random text';
        Storage::fake('sheet-base');
        Storage::disk('sheet-base')->put($path, $content);
        $file = new class extends StorageFileReader
        {
            public string $path = '/testfile.txt';

            protected string $extension = 'txt';

            public function publicStorageGet(string $path): string
            {
                return $this->storageGet($path);
            }

            /**
             * get data and fill it into the pipeline
             */
            public function fillPipeline(PipelineData &$pipelineData): void
            {
                // TODO: Implement fillPipeline() method.
            }
        };
        $this->assertEquals($content, $file->publicStorageGet($path));
    }
}
