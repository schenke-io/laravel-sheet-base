<?php

namespace SchenkeIo\LaravelSheetBase\Exceptions;

/**
 * Exception thrown when an error occurs with a Google Sheet.
 */
class GoogleSheetException extends ClassSyntaxException
{
    public static function spreadSheetIdNotDefined(string $className): GoogleSheetException
    {
        return new self($className, 'spreadsheetId not defined');
    }

    public static function sheetNameNotDefined(string $className): GoogleSheetException
    {
        return new self($className, 'sheetName not defined');
    }
}
