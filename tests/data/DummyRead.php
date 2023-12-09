<?php

namespace SchenkeIo\LaravelSheetBase\Tests\data;

use SchenkeIo\LaravelSheetBase\Endpoints\EndpointReadArray;

class DummyRead extends EndpointReadArray
{
    public function getArray(): array
    {
        // this needs to be overwritten
        return [
            ['id' => 1, 'name' => 'alpha'],
            ['id' => 2, 'name' => 'beta'],
        ];
    }
}
