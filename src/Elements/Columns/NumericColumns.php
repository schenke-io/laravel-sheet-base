<?php

namespace SchenkeIo\LaravelSheetBase\Elements\Columns;

use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;

trait NumericColumns
{
    /**
     * @throws SchemaAddColumnException
     */
    public function addUnsigned(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Unsigned));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addUnsignedNotNull(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::UnsignedNotNull));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addFloat(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Float));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addBool(string $name): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Boolean));
    }
}
