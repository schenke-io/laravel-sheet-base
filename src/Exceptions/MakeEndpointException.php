<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

use Exception;

/**
 * Exception thrown when an error occurs while creating an endpoint.
 */
class MakeEndpointException extends Exception
{
    public function __construct(string $identifier, string $msg)
    {
        parent::__construct(sprintf("at '%s' > %s", $identifier, $msg));
    }
}
