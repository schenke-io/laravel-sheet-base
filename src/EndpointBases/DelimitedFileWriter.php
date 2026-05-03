<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use Spatie\SimpleExcel\SimpleExcelWriter;

/**
 * Class DelimitedFileWriter
 *
 * Base class for writing delimited files (CSV, TSV, PSV) from the pipeline.
 *
 * Main Responsibilities:
 * - File Serialization: Uses Spatie SimpleExcelWriter to write data to delimited formats.
 * - Delimiter Management: Ensures a valid delimiter is used for writing.
 * - Pipeline Release: Converts pipeline data into a delimited format and saves it to storage.
 *
 * Usage Example:
 * ```php
 * class CsvWriter extends DelimitedFileWriter {
 *     protected string $extension = 'csv';
 *     protected string $delimiter = ',';
 * }
 * ```
 */
class DelimitedFileWriter extends StorageFileWriter
{
    /**
     * needs to be overwritten
     */
    protected string $delimiter = '';

    public function __construct(?string $path = null)
    {
        parent::__construct($path);
        if ($this->delimiter == '') {
            throw new EndpointCodeException(class_basename($this), 'delimiter cannot be empty');
        }
    }

    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $idName = $pipelineData->sheetBaseSchema->idName;
        $tmpFile = tempnam(sys_get_temp_dir(), 'csv');
        $writer = SimpleExcelWriter::create($tmpFile, 'csv', null, $this->delimiter, false);
        foreach ($pipelineData->toArray() as $index => $row) {
            $row = array_merge([$idName => $index], $row); // add index to the beginning
            $writer->addRow($row);
        }
        $this->storagePut($this->path, File::get($tmpFile));
    }
}
