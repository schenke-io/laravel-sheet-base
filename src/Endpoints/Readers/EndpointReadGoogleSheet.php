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
        $headers = [];
        foreach ($data as $rowIndex => $row) {
            if ($rowIndex == 0) {
                $headers = $row;
            } else {
                $pipelineData->addRow(array_combine($headers, $row));
            }
        }
    }
}
