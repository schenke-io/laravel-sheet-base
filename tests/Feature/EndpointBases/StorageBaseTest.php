<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageBase;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;

class StorageBaseTest extends TestCase
{
    public function test_sheet_base_is_not_defined_exception(): void
    {
        $this->expectException(FileSystemNotDefinedException::class);
        $file = new class extends StorageBase
        {
            /**
             * brief text what this endpoint is doing
             */
            public function explain(): string
            {
                return 'in test';
            }
        };
    }

    public function test_to_string()
    {
        Config::set('filesystems.disks.'.StorageBase::DEFAULT_DISK, []);
        $file = new class extends StorageBase
        {
            /**
             * brief text what this endpoint is doing
             */
            public function explain(): string
            {
                return 'in test';
            }
        };
        $this->assertIsString($file->toString());
    }
}
