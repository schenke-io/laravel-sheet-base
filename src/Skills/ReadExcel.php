<?php

namespace SchenkeIo\LaravelSheetBase\Skills;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

trait ReadExcel
{
    //    /**
    //     * @throws ReadParseException
    //     */
    //    protected function readExcelText(string $filepath, string $separator, SheetBaseSchema $sheetBaseSchema): PipelineData
    //    {
    //        $content = Storage::get($filepath);
    //        if (is_null($content)) {
    //            throw new ReadParseException("no valid content from $filepath");
    //        }
    //        $headers = [];
    //        $pipelineData = new PipelineData($sheetBaseSchema);
    //        foreach (explode(PHP_EOL, $content) as $index => $line) {
    //            if (trim($line) == '') {
    //                continue;
    //            }
    //            $row = str_getcsv($line, $separator);
    //            if (empty($headers)) {
    //                $headers = $row;
    //            } else {
    //                array_splice($row, count($headers));
    //                $pipelineData->addRow(array_combine($headers, $row));
    //            }
    //        }
    //
    //        return $pipelineData;
    //    }

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
