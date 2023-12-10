<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements\EndpointBases;

use SchenkeIo\LaravelSheetBase\Elements\EndpointBases\StorageTree;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class StorageTreeTest extends ConfigTestCase
{
    public function testEmptyRootReadParseException()
    {
        $this->expectException(ReadParseException::class);
        $tree = new class extends StorageTree
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
