<?php

namespace SchenkeIo\LaravelSheetBase\Tests\data;

use SchenkeIo\LaravelSheetBase\Endpoints\EndpointReadArray;

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
