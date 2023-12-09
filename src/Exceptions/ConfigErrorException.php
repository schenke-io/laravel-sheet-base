<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

use Exception;

class ConfigErrorException extends Exception
{
    public function __construct(string $pipelineName, string $msg)
    {
        parent::__construct(sprintf("in pipeline '%s': %s", $pipelineName, $msg));
    }
}
