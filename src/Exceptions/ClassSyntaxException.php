<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

use Exception;

class ClassSyntaxException extends Exception
{
    public function __construct(string $className, string $message)
    {
        parent::__construct(sprintf('syntax error in %s: %s', $className, $message));
    }
}
