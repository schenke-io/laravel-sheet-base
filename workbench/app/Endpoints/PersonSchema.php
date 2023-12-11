<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

class PersonSchema extends SheetBaseSchema
{
    /**
     * define the schema in Laravel migration syntax
     *
     * @throws SchemaDefinitionException
     */
    public function define(): void
    {
        $this->addId();
        $this->addString('name');
    }
}
