<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadArray;

class LangFilter extends EndpointReadArray
{
    public function getArray(): array
    {
        // this needs to be overwritten
        return [
            ['id' => 'home.title'],
            ['id' => 'home.description'],
        ];
    }
}
