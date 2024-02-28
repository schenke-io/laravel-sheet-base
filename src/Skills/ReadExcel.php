<?php

namespace SchenkeIo\LaravelSheetBase\Skills;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

trait ReadExcel
{
    protected function getCsvLines(string $content, string $separator, SheetBaseSchema $sheetBaseSchema): array
    {
        $return = [];
        $headers = [];
        $tempTable = new PipelineData($sheetBaseSchema);
        foreach (explode(PHP_EOL, $content) as $index => $line) {
            if (trim($line) == '') {
                continue;
            }
            $row = str_getcsv($line, $separator);
            if (empty($headers)) {
                $headers = $row;
            } else {
                array_splice($row, count($headers));
                $return[] = array_combine($headers, $row);
            }
        }

        return $return;
    }
}
