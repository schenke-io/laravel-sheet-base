<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

class LangSchema extends SheetBaseSchema
{
    /**
     * define the schema in Laravel migration syntax
     *
     * @throws SchemaDefinitionException
     */
    protected function define(): void
    {
        $this->addDot();
        $this->addLanguage('de');
        $this->addLanguage('en');
    }
}
