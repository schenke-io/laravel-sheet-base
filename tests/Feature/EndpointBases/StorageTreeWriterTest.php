<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageTreeWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class StorageTreeWriterTest extends ConfigTestCase
{
    public static function dataProviderPath(): array
    {
        return [
            'path under root' => ['/something/testfile.txt', true],
            'path outside root' => ['/outside/testfile.txt', false],
        ];
    }

    #[DataProvider('dataProviderPath')]
    public function testStoragePut(string $path, bool $isOk): void
    {
        $content = 'some random text';
        Storage::fake('sheet-base');
        if (! $isOk) {
            $this->expectException(EndpointCodeException::class);
        }

        $tree = new class extends StorageTreeWriter
        {
            public string $root = '/something';

            public array $fileBases = ['a'];

            public function publicStoragePut(string $path, string $content): void
            {
                $this->storagePut($path, $content);
            }

            public function releasePipeline(PipelineData $pipelineData, string $writingClass)
            {
                // TODO: Implement releasePipeline() method.
            }
        };
        $tree->publicStoragePut($path, $content);
        $this->assertEquals($content, Storage::disk('sheet-base')->get($path));
    }
}
