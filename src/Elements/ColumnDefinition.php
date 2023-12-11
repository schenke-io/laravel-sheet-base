<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

class ColumnDefinition
{
    public function __construct(
        public ColumnType $type
    )
    {
    }
}