<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageBase;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;

class StorageBaseTest extends TestCase
{
    public function testSheetBaseIsNotDefinedException(): void
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
}
