<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteJson;

class PersonsWriteJson extends EndpointWriteJson
{
    public string $path = '/persons.json';
}
