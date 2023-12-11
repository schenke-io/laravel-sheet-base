<?php

namespace SchenkeIo\LaravelSheetBase\Elements\Columns;

use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;

trait TextColumns
{
    /**
     * @throws SchemaAddColumnException
     */
    public function addString(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::String));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addLanguage(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Language));
    }
}
