<?php

namespace SchenkeIo\LaravelSheetBase\Google;

use Google\Service\Exception as Google_Service_Expectation;
use Google_Service_Sheets_Request;
use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadGoogleSheet;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleServiceException;
use SchenkeIo\LaravelSheetBase\Helpers\Chunks;

class GoogleBackgroundPainter
{
    public function __construct(
        protected Command $cmd,
        protected string $namePipeline,
        protected IsReader $source
    ) {}

    public static function take(Command $cmd, string $namePipeline, IsReader $source): self
    {
        return new self($cmd, $namePipeline, $source);
    }

    /**
     * @throws GoogleServiceException
     */
    public function markRed(array $keysToRemove): void
    {
        $keyCount = count($keysToRemove);
        if ($keyCount == 0) {
            return;
        }
        /** @var EndpointReadGoogleSheet $sheet */
        $sheet = $this->source; // Implicit upcasting

        /*
         * read first row, find id
         */
        $sourceKeys = array_map(fn ($x) => $x[0], $sheet->get('A2:A'));
        $requests = [];
        $sheetId = $sheet->getSheetId();

        foreach ($sourceKeys as $row => $key) {
            if (in_array($key, $keysToRemove)) {
                $requests[] = $this->getRedBackgroundRequest($sheetId, $row);
            }
        }

        /*
         * now send it all in batches
         */
        $batches = Chunks::splitIntoBatches($requests);
        //        dd($sourceKeys,array_keys($requests),array_keys($batches));

        foreach ($batches as $batchIndex => $batch) {
            try {
                $sheet->googleSheetApi
                    ->batchUpdate($sheet->spreadsheetId, $batch);
                $this->cmd->info("batch $batchIndex updated.");
            } catch (Google_Service_Expectation $e) {
                $this->cmd->error("batch $batchIndex failed: ".$e->getMessage());
            }
        }
        $this->cmd->info('');
    }

    protected function getRedBackgroundRequest(int $sheetId, int $row): Google_Service_Sheets_Request
    {
        return new Google_Service_Sheets_Request([
            'repeatCell' => [
                'cell' => [
                    'userEnteredFormat' => [
                        'backgroundColor' => [
                            'red' => 1,
                            'green' => 0,
                            'blue' => 0,
                        ],
                    ],
                ],
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => $row + 1,
                    'endRowIndex' => $row + 2,
                    'startColumnIndex' => 0,
                    'endColumnIndex' => 1,
                ],
                'fields' => 'userEnteredFormat',
            ],
        ]);
    }
}
