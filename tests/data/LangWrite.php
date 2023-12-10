<?php

namespace SchenkeIo\LaravelSheetBase\Tests\data;

use SchenkeIo\LaravelSheetBase\Endpoints\EndpointWriteLang;

class LangWrite extends EndpointWriteLang
{
    public string $root = '/';

    public array $fileBases = ['home'];
}
