<?php

namespace SchenkeIo\LaravelSheetBase\Tests\data;

use SchenkeIo\LaravelSheetBase\Endpoints\EndpointWriteNeon;

class PersonsWriteNeon extends EndpointWriteNeon
{
    public string $path = '/persons.neon';
}
