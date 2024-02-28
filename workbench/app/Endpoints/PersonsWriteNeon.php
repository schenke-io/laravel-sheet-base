<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteNeon;

class PersonsWriteNeon extends EndpointWriteNeon
{
    public string $path = '/persons.neon';
}
