<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use Google\Service\Exception;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\GoogleSheetBase;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

class EndpointReadGoogleSheet extends GoogleSheetBase implements IsReader
{
    /**
     * brief text what this endpoint is doing
     */
    public function explain(): string
    {
        return 'reads a google sheet';
    }

    /**
     * get data and fill it into the pipeline
     *
     * @throws ReadParseException
     * @throws Exception
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        $data = $this->spreadsheet->getData($this->spreadsheetId, $this->sheetName);
        $header = [];
        foreach ($data as $rowIndex => $row) {
            if ($rowIndex == 0) {
                $header = $row;
            } else {
                // check if first column has a value
                if (strlen($row[0]) < 1) {
                    continue;
                }
                // Align $row to the size of $header
                $row = array_slice($row, 0, count($header)); // Trim if $row is too long
                $row = array_pad($row, count($header), null); // Extend with null if $row is too short
                // Combine the arrays
                $pipelineData->addRow(array_combine($header, $row));
            }
        }
    }
}
