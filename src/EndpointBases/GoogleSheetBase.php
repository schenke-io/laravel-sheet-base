<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleServiceException;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleSheetException;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;
use Throwable;

abstract class GoogleSheetBase implements IsEndpoint
{
    public string $spreadsheetId = '';

    public string $sheetName = '';

    public GoogleSheetApi $googleSheetApi;

    /**
     * @throws GoogleSheetException
     * @throws Throwable
     */
    public function __construct()
    {
        $className = class_basename($this);
        if (strlen($this->spreadsheetId) == 0) {
            throw GoogleSheetException::spreadSheetIdNotDefined($className);
        }
        if (strlen($this->sheetName) < 1) {
            throw GoogleSheetException::sheetNameNotDefined($className);
        }
        /*
         * replace spreadsheetId with config value if it is a valid key
         */
        $configValue = config('sheet-base.spreadsheets.'.$this->spreadsheetId, '');
        if ($configValue) {
            $this->spreadsheetId = $configValue;
        }
        $this->googleSheetApi = new GoogleSheetApi;
    }

    /**
     * @throws GoogleServiceException
     */
    public function get(string $range = ''): array
    {
        if ($range == '') {
            $range = $this->sheetName;
        } else {
            $range = $this->sheetName.'!'.$range;
        }

        return $this->googleSheetApi->getData($this->spreadsheetId, $range);
    }

    final public function toString(): string
    {
        return 'GoogleSheet:'.substr($this->spreadsheetId, 0, 4).'...'.substr($this->spreadsheetId, -4);
    }

    public function getSheetId(): int
    {
        return $this->googleSheetApi->getSheetId($this->spreadsheetId, $this->sheetName);
    }
}
