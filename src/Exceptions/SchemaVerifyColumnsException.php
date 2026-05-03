<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

/**
 * Exception thrown when a schema column verification fails.
 */
class SchemaVerifyColumnsException extends \Exception
{
    public function __construct(string $identifier, string $msg)
    {
        parent::__construct(sprintf("at '%s' > %s", $identifier, $msg));
    }
}
