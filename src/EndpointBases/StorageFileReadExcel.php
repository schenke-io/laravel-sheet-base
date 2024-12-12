<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use Spatie\SimpleExcel\SimpleExcelReader;

class StorageFileReadExcel extends StorageFileReader
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
        $tmpFile = tempnam(sys_get_temp_dir(), 'csv');
        File::put($tmpFile, $this->storageGet($this->path));
        SimpleExcelReader::create($tmpFile, 'csv')
            ->useDelimiter($this->delimiter)
            ->getRows()
            ->each(
                fn (array $row) => $pipelineData->addRow($row)
            );
    }
}
