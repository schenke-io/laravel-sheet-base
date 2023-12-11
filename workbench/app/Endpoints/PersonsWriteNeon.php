<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\EndpointWriteNeon;

class PersonsWriteNeon extends EndpointWriteNeon
{
    public string $path = '/persons.neon';
}
