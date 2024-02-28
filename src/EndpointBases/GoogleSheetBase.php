<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleSheetException;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;
use Throwable;

abstract class GoogleSheetBase implements IsEndpoint
{
    public string $spreadsheetId = '';

    public string $sheetName = '';

    //    protected GoogleSheetApi $spreadsheet;
    public GoogleSheetApi $spreadsheet;

    /**
     * @throws GoogleSheetException
     * @throws Throwable
     */
    public function __construct()
    {
        throw_if(strlen($this->spreadsheetId) < 10, new GoogleSheetException('spreadsheetId not defined'));
        throw_if(strlen($this->sheetName) < 1, new GoogleSheetException('sheetName not defined'));
        $this->spreadsheet = new GoogleSheetApi();
    }
}
