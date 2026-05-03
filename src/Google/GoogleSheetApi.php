<?php

namespace SchenkeIo\LaravelSheetBase\Google;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Exception;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request;
use Google\Service\Sheets\ValueRange;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleServiceException;

/**
 * Class GoogleSheetApi
 *
 * Wrapper for the Google Sheets API, providing simplified methods for reading and writing sheet data.
 *
 * Main Responsibilities:
 * - Authentication: Uses application default credentials and sets appropriate scopes.
 * - Data Retrieval: Fetches values from a specified spreadsheet and sheet.
 * - Data Updates: Updates cell values and performs batch operations (e.g., formatting).
 * - Metadata: Retrieves sheet IDs based on title.
 *
 * Usage Example:
 * ```php
 * $api = new GoogleSheetApi();
 * $data = $api->getData('spreadsheet-id', 'Sheet1');
 * ```
 */
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
     * @return array<int, array<int, mixed>>
     *
     * @throws GoogleServiceException
     */
    public function getData(string $spreadsheetId, string $sheetName): array
    {
        try {
            $values = $this->sheets
                ->spreadsheets_values
                ->get($spreadsheetId, $sheetName)
                ->getValues();

            if (! is_array($values)) {
                return [];
            }

            /** @var array<int, array<int, mixed>> $values */
            return $values;
        } catch (Exception $e) {
            throw GoogleServiceException::fromGetValueRange($e->getMessage());
        }
    }

    /**
     * @param  array<int, array<int, mixed>>  $values
     * @param  'ROWS'|'COLUMNS'  $majorDimension
     * @param  array<string, mixed>  $optParams
     *
     * @throws GoogleServiceException
     */
    public function putData(string $spreadsheetId, string $range, array $values, string $majorDimension = 'ROWS', array $optParams = []): Sheets\UpdateValuesResponse
    {
        try {
            $valueRange = new ValueRange;
            $valueRange->setValues($values);
            $valueRange->setMajorDimension($majorDimension);
            $optParams['valueInputOption'] = 'RAW';

            return $this->sheets->spreadsheets_values->update($spreadsheetId, $range, $valueRange, $optParams);
        } catch (Exception $e) {
            throw GoogleServiceException::fromUpdateValueResponse($e->getMessage());
        }
    }

    /**
     * @param  Request[]  $requests
     *
     * @throws GoogleServiceException
     */
    public function batchUpdate(string $spreadsheetId, array $requests): void
    {
        try {
            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
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
