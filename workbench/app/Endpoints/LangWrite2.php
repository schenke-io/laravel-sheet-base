<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteLang;

class LangWrite2 extends EndpointWriteLang
{
    public string $root = '/lang';

    public array $fileBases = ['home'];
}
