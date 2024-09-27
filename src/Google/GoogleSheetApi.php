<?php

namespace SchenkeIo\LaravelSheetBase\Google;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Exception;
use Google\Service\Sheets;
use Google\Service\Sheets\Resource\SpreadsheetsValues;
use Google\Service\Sheets\ValueRange;

class GoogleSheetApi
{
    public SpreadsheetsValues $spreadsheetsValues;

    public function __construct()
    {
        $client = new Client;
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $this->spreadsheetsValues = (new Sheets($client))->spreadsheets_values;
    }

    /**
     * @throws Exception
     */
    public function getData(string $spreadsheetId, string $sheetName): array
    {
        return $this->spreadsheetsValues
            ->get($spreadsheetId, $sheetName)
            ->getValues();
    }

    /**
     * @throws Exception
     */
    public function putData(string $spreadsheetId, string $range, array $values, string $majorDimension = 'ROWS', $optParams = []): Sheets\UpdateValuesResponse
    {
        $valueRange = new ValueRange;
        $valueRange->setValues([$values]);
        $valueRange->setMajorDimension($majorDimension);
        $optParams['valueInputOption'] = 'RAW';

        return $this->spreadsheetsValues->update($spreadsheetId, $range, $valueRange, $optParams);
    }
}
