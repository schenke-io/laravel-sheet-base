<?php

namespace SchenkeIo\LaravelSheetBase\Elements\Columns;

use Closure;
use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;

trait ControlColumns
{
    /**
     * @throws SchemaAddColumnException
     */
    public function addId(string $name = 'id'): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::ID));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addDot(string $name = 'id'): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::Dot));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addClosure(string $name, ?Closure $closure = null): void
    {
        $this->newColumn($name, new ColumnSchema(type: ColumnType::Closure, closure: $closure));
    }
}
