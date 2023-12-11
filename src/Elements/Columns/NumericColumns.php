<?php

namespace SchenkeIo\LaravelSheetBase\Elements\Columns;

use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

trait NumericColumns
{
    /**
     * @throws SchemaDefinitionException
     */
    public function addUnsigned(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Unsigned));
    }

    /**
     * @throws SchemaDefinitionException
     */
    public function addFloat(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Float));
    }

    /**
     * @throws SchemaDefinitionException
     */
    public function addBool(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Boolean));
    }
}
