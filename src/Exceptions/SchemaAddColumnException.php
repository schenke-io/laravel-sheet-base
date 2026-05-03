<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

/**
 * Exception thrown when an error occurs while adding a column to a schema.
 */
class SchemaAddColumnException extends \Exception
{
    public function __construct(string $identifier, string $msg)
    {
        parent::__construct(sprintf("at '%s' > %s", $identifier, $msg));
    }
}
