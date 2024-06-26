<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadArray;

class LangRead extends EndpointReadArray
{
    public function getArray(): array
    {
        // this needs to be overwritten
        return [
            ['id' => 'home.title', 'de' => 'Startseite', 'en' => 'Homepage'],
            ['id' => 'home.description', 'de' => 'Startseite', 'en' => 'Homepage'],
            ['id' => 'home.keywords', 'de' => 'Startseite', 'en' => 'Homepage'],
        ];
    }
}
