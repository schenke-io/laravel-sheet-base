<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Closure;

class ColumnSchema
{
    public Closure $transform;

    public function __construct(public ColumnType $type, public ?Closure $closure = null)
    {
    }

    public function format(string $key, array $row): mixed
    {
        return $this->type->format($this->transform($key, $row));
    }

    public function transform(string $key, array $row): mixed
    {
        if (is_null($this->closure)) {
            return $row[$key] ?? null;
        } else {
            return ($this->closure)($key, $row);
        }
    }
}
