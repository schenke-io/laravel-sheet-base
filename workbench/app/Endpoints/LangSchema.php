<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;

class LangSchema extends SheetBaseSchema
{
    /**
     * define the schema in Laravel migration syntax
     *
     * @throws SchemaAddColumnException
     */
    protected function define(): void
    {
        $this->addDot();
        $this->addLanguage('de');
        $this->addLanguage('en');
    }
}
