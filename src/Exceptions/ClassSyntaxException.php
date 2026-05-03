<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

use Exception;

/**
 * Exception thrown when a class syntax error occurs.
 */
class ClassSyntaxException extends Exception
{
    public function __construct(string $className, string $message)
    {
        parent::__construct(sprintf('syntax error in %s: %s', $className, $message));
    }
}
