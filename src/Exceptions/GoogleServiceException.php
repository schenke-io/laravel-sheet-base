<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

use Google\Service\Exception;

/**
 * Exception thrown when an error occurs with the Google Service.
 */
class GoogleServiceException extends Exception
{
    public static function fromGetValueRange(string $getMessage): GoogleServiceException
    {
        return new self($getMessage);
    }

    public static function fromUpdateValueResponse(string $getMessage): GoogleServiceException
    {
        return new self($getMessage);
    }

    public static function fromBatchupdateResponse(string $getMessage): GoogleServiceException
    {
        return new self($getMessage);
    }

    public static function fromGetSpreadsheet(string $getMessage): GoogleServiceException
    {
        return new self($getMessage);
    }
}
