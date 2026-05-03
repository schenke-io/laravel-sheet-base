<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\GoogleSheetBase;

class EndpointWriteGoogleSheet extends GoogleSheetBase implements IsWriter
{
    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $schema = $pipelineData->sheetBaseSchema;
        $idName = $schema->getIdName();
        $columns = array_keys($schema->getColumns());

        $header = $columns;
        $data = [$header];

        foreach ($pipelineData->toArray() as $id => $row) {
            $dataRow = [];
            foreach ($columns as $column) {
                if ($column === $idName) {
                    $dataRow[] = $id;
                } else {
                    $dataRow[] = $row[$column] ?? '';
                }
            }
            $data[] = $dataRow;
        }

        $this->googleSheetApi->putData($this->spreadsheetId, $this->sheetName, $data);
    }

    public function explain(): string
    {
        return "writes into Google Sheet '$this->sheetName' in spreadsheet '$this->spreadsheetId'";
    }
}
