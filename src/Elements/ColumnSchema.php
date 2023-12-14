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
        if (is_null($this->closure)) {
            $value = $row[$key] ?? null;
        } else {
            $value = ($this->closure)($key, $row);
        }

        return $this->type->format($value);
    }
}
