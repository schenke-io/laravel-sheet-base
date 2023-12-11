<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

class SchemaAddColumnException extends \Exception
{
    public function __construct(string $identifier, string $msg)
    {
        parent::__construct(sprintf("at '%s' > %s", $identifier, $msg));
    }
}
