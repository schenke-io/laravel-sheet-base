<?php

namespace SchenkeIo\LaravelSheetBase\Tests\data;

use SchenkeIo\LaravelSheetBase\Endpoints\EndpointReadPsv;

class PersonsReadPsv extends EndpointReadPsv
{
    public string $path = '/persons.psv';
}
