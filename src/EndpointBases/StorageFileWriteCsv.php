<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use Spatie\SimpleExcel\SimpleExcelWriter;

class StorageFileWriteCsv extends StorageFileWriter
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
