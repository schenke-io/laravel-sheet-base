<?php

namespace SchenkeIo\LaravelSheetBase\Elements\Columns;

use Closure;
use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;


trait ControlColumns
{
    /**
     * @throws SchemaDefinitionException
     */
    public function addId(string $name = 'id'): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::ID));
    }

    /**
     * @throws SchemaDefinitionException
     */
    public function addDot(string $name = 'id'): void
    {
        $this->addColumn($name, new ColumnSchema(ColumnType::Dot));
    }

    /**
     * @throws SchemaDefinitionException
     */
    public function addClosure(string $name, ?Closure $closure = null): void
    {
        $this->addColumn($name, new ColumnSchema(type: ColumnType::Closure, closure: $closure));
    }
}