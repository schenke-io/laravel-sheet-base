<?php

namespace SchenkeIo\LaravelSheetBase\Contracts;

interface IsEndpoint
{
    /**
     * brief text what this endpoint is doing
     */
    public function explain(): string;

    /**
     * name or identifier of this endpoint
     */
    public function toString(): string;
}
