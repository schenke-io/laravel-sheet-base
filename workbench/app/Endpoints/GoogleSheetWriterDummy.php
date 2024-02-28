<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\EndpointBases\GoogleSheetWriter;

class GoogleSheetWriterDummy extends GoogleSheetWriter
{
    public string $spreadsheetId = 'abcdefghijklmn';

    public int $sheetIndex = 0;

    public function explain(): string
    {
        return 'test';
    }

    public function releasePipeline(PipelineData $pipelineData, string $writingClass)
    {
    }

    /**
     * @throws \Throwable
     */
    public function dummyWriteKeys(array $array): void
    {
        $this->writeKeys($array);
    }

    /**
     * @throws \Throwable
     */
    public function dummyWriteSchema(SheetBaseSchema $schema): void
    {
        $this->writeSchema($schema);
    }
}
