<?php

namespace SchenkeIo\LaravelSheetBase\Elements\Columns;

use Closure;
use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Enums\ColumnType;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;

trait HasColumns
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

    /**
     * @throws SchemaAddColumnException
     */
    public function addUnsigned(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::Unsigned));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addUnsignedNotNull(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::UnsignedNotNull));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addFloat(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::Float));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addBool(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::Boolean));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addString(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::String));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addLanguage(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::Language));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addNullString(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::NullString));
    }

    /**
     * @throws SchemaAddColumnException
     */
    public function addDateTime(string $name): void
    {
        $this->newColumn($name, new ColumnSchema(ColumnType::DateTime));
    }
}
