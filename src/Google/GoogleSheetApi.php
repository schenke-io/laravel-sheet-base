<?php

namespace SchenkeIo\LaravelSheetBase\Google;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Exception;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_Request;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleServiceException;

class GoogleSheetApi
{
    public Sheets $sheets;

    public function __construct(protected Client $client = new Client)
    {
        $this->client->useApplicationDefaultCredentials();
        $this->client->addScope(Drive::DRIVE);
        $this->sheets = new Sheets($this->client);
    }

    /**
     * @return array[]
     *
     * @throws GoogleServiceException
     */
    public function getData(string $spreadsheetId, string $sheetName): array
    {
        try {
            return $this->sheets
                ->spreadsheets_values
                ->get($spreadsheetId, $sheetName)
                ->getValues();
        } catch (Exception $e) {
            throw GoogleServiceException::fromGetValueRange($e->getMessage());
        }
    }

    /**
     * @throws GoogleServiceException
     */
    public function putData(string $spreadsheetId, string $range, array $values, string $majorDimension = 'ROWS', array $optParams = []): Sheets\UpdateValuesResponse
    {
        try {
            $valueRange = new ValueRange;
            $valueRange->setValues([$values]);
            $valueRange->setMajorDimension($majorDimension);
            $optParams['valueInputOption'] = 'RAW';

            return $this->sheets->spreadsheets_values->update($spreadsheetId, $range, $valueRange, $optParams);
        } catch (Exception $e) {
            throw GoogleServiceException::fromUpdateValueResponse($e->getMessage());
        }
    }

    /**
     * @param  Google_Service_Sheets_Request[]  $requests
     *
     * @throws GoogleServiceException
     */
    public function batchUpdate(string $spreadsheetId, array $requests): void
    {
        try {
            $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => $requests,
            ]);
            $this->sheets->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
        } catch (Exception $e) {
            throw GoogleServiceException::fromBatchupdateResponse($e->getMessage());
        }
    }

    /**
     * @throws GoogleServiceException
     */
    public function getSheetId(string $spreadsheetId, string $sheetName): int
    {
        try {
            $sheets = $this->sheets->spreadsheets->get($spreadsheetId)->getSheets();
            foreach ($sheets as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    return $sheet->getProperties()->getSheetId();
                }
            }

            return -1;
        } catch (Exception $e) {
            throw GoogleServiceException::fromGetSpreadsheet($e->getMessage());
        }
    }
}
