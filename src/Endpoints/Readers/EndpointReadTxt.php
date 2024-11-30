<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReader;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;

class EndpointReadTxt extends StorageFileReader
{
    protected string $extension = 'txt';

    /**
     * get data and fill it into the pipeline
     *
     * @throws DataReadException
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        $fileContent = $this->storageGet($this->path);
        if (is_null($fileContent)) {
            throw new DataReadException('File not found in storage: '.$this->path);
        }
        foreach (explode("\n", $fileContent) as $line) {
            $line = trim($line);
            if ($line !== '') {
                // the data field is a dummy as text files have no data just keys
                $pipelineData->addRow(['id' => $line]);
            }
        }
    }
}
