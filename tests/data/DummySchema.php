<?php

namespace SchenkeIo\LaravelSheetBase\Tests\data;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

class DummySchema extends SheetBaseSchema
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
