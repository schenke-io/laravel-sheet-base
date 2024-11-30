<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadArray;

class DummyFilter extends EndpointReadArray
{
    public function getArray(): array
    {
        return [
            [1],
            [7],
        ];
    }
}
