<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\GoogleSheetBase;

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
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        $data = $this->googleSheetApi->getData($this->spreadsheetId, $this->sheetName);
        $header = [];
        foreach ($data as $rowIndex => $row) {
            if ($rowIndex == 0) {
                // the right end of the header is the first empty field
                foreach ($row as $value) {
                    $valueStr = (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) ? (string) $value : '';
                    if ($valueStr === '') {
                        break;
                    }
                    $header[] = $valueStr;
                }
            } else {
                // end when the first column has no value
                $firstValue = $row[0] ?? '';
                $firstValueStr = (is_scalar($firstValue) || (is_object($firstValue) && method_exists($firstValue, '__toString'))) ? (string) $firstValue : '';
                if ($firstValueStr === '') {
                    break;
                }
                // Align $row to the size of $header
                $row = array_slice($row, 0, count($header)); // Trim if $row is too long
                $row = array_pad($row, count($header), null); // Extend with null if $row is too short
                // Combine the arrays
                /** @var array<string, mixed> $combined */
                $combined = array_combine($header, $row) ?: [];
                $pipelineData->addRow($combined);
            }
        }
    }
}
