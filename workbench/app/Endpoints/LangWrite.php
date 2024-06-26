<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteLang;

class LangWrite extends EndpointWriteLang
{
    public string $root = '/lang';

    public array $fileBases = ['home'];
}
