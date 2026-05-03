<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use Spatie\SimpleExcel\SimpleExcelReader;

/**
 * Class DelimitedFileReader
 *
 * Base class for reading delimited files (CSV, TSV, PSV) into the pipeline.
 *
 * Main Responsibilities:
 * - File Parsing: Uses Spatie SimpleExcelReader to parse delimited files.
 * - Delimiter Management: Ensures a valid delimiter is defined for the file type.
 * - Storage Access: Retrieves file content from storage and processes it via a temporary local file.
 *
 * Usage Example:
 * ```php
 * class CsvReader extends DelimitedFileReader {
 *     protected string $extension = 'csv';
 *     protected string $delimiter = ',';
 * }
 * ```
 */
class DelimitedFileReader extends StorageFileReader
{
    protected string $extension = '';

    protected string $delimiter = '';

    public function __construct(?string $path = null)
    {
        parent::__construct($path);
        $classname = class_basename($this);
        if ($this->delimiter == '') {
            throw new EndpointCodeException($classname, '$delimiter is not defined');
        }
    }

    /**
     * get data and fill it into the pipeline     *
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        $tmpFile = $this->getTempFile();
        if ($tmpFile === false) {
            return;
        }
        File::put($tmpFile, (string) $this->storageGet($this->path));
        SimpleExcelReader::create($tmpFile, 'csv')
            ->useDelimiter($this->delimiter)
            ->getRows()
            ->each(
                function (mixed $row) use ($pipelineData) {
                    /** @var array<string, mixed> $rowArray */
                    $rowArray = (array) $row;
                    $pipelineData->addRow($rowArray);
                }
            );
    }

    protected function getTempFile(): string|false
    {
        return tempnam(sys_get_temp_dir(), 'csv');
    }
}
