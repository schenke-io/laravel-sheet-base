<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;

class DummySchema extends SheetBaseSchema
{
    /**
     * define the schema in Laravel migration syntax
     *
     * @throws SchemaAddColumnException
     */
    public function define(): void
    {
        $this->addId();
        $this->addString('name');
    }
}
