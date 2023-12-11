<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

use Exception;

class MakeEndpointException extends Exception
{
    public function __construct(string $identifier, string $msg)
    {
        parent::__construct(sprintf("at '%s' > %s", $identifier, $msg));
    }
}
