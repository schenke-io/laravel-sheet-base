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

    public GoogleSheetApi $spreadsheet;

    /**
     * @throws GoogleSheetException
     * @throws Throwable
     */
    public function __construct()
    {
        $className = class_basename($this);
        throw_if(strlen($this->spreadsheetId) == 0, new GoogleSheetException($className, 'spreadsheetId not defined'));
        throw_if(strlen($this->sheetName) < 1, new GoogleSheetException($className, 'sheetName not defined'));
        /*
         * replace spreadsheetId with config value if it is a valid key
         */
        $configValue = config('sheet-base.spreadsheets.'.$this->spreadsheetId, '');
        if ($configValue) {
            $this->spreadsheetId = $configValue;
        }
        $this->spreadsheet = new GoogleSheetApi;
    }
}
