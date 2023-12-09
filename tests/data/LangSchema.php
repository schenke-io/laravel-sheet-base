<?php

namespace SchenkeIo\LaravelSheetBase\Tests\data;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

class LangSchema extends SheetBaseSchema
{
    /**
     * define the schema in Laravel migration syntax
     */
    protected function define(): void
    {
        $this->addDot();
        $this->addLanguage('de');
        $this->addLanguage('en');
    }
}
