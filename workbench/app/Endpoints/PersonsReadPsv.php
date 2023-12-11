<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\EndpointReadPsv;

class PersonsReadPsv extends EndpointReadPsv
{
    public string $path = '/psv/persons.psv';
}
