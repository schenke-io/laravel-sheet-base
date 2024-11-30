<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFile;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class StorageFileTest extends ConfigTestCase
{
    public function test_path_not_set(): void
    {
        $this->expectException(EndpointCodeException::class);
        $file = new class extends StorageFile
        {
            public string $path = '';

            public string $extension = 'txt';

            /**
             * brief text what this endpoint is doing
             */
            public function explain(): string
            {
                return 'in test';
            }
        };
    }

    public function test_extension_not_set(): void
    {
        $this->expectException(EndpointCodeException::class);
        $file = new class extends StorageFile
        {
            public string $path = 'test.txt';

            public string $extension = '';

            /**
             * brief text what this endpoint is doing
             */
            public function explain(): string
            {
                return 'in test';
            }
        };
    }

    public function test_wrong_extension_set(): void
    {
        $this->expectException(EndpointCodeException::class);
        $file = new class extends StorageFile
        {
            public string $path = 'test.txt';

            public string $extension = 'abc';

            /**
             * brief text what this endpoint is doing
             */
            public function explain(): string
            {
                return 'in test';
            }
        };
    }

    public function test_storage_exists(): void
    {
        $path = 'testfile.txt';
        Storage::fake('sheet-base');
        $this->assertFalse(Storage::disk('sheet-base')->exists($path));
        Storage::disk('sheet-base')->put($path, 'some text');
        $this->assertTrue(Storage::disk('sheet-base')->exists($path));

    }

    public function test_get_storage_root(): void
    {

        $file = new class extends StorageFile
        {
            public string $path = 'test.txt';

            public string $extension = 'txt';

            public function getRoot(): string
            {
                return $this->getStorageRoot();
            }

            /**
             * brief text what this endpoint is doing
             */
            public function explain(): string
            {
                return 'in test';
            }
        };
        $this->assertIsString($file->getRoot());
    }
}
