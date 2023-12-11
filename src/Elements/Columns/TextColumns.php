<?php

namespace SchenkeIo\LaravelSheetBase\Elements\Columns;


use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

trait TextColumns
{
    /**
     * @throws SchemaDefinitionException
     */
    public function addString(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::String));
    }

    /**
     * @throws SchemaDefinitionException
     */
    public function addLanguage(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Language));
    }
}