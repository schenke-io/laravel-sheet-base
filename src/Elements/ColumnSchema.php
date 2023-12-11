<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Closure;

class ColumnSchema
{
    public Closure $transform;

    public function __construct(public ColumnType $type, public ?Closure $closure = null)
    {
    }

    public function format(mixed $param, array $row): mixed
    {
        return $this->type->format($this->transform($param, $row));
    }

    public function transform(mixed $param, array $row): mixed
    {
        if (is_null($this->closure)) {
            return $param;
        } else {
            return ($this->closure)($param, $row);
        }
    }
}
