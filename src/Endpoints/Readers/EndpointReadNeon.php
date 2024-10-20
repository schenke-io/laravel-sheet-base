<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use Nette\Neon\Exception as NeonException;
use Nette\Neon\Neon;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReader;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

class EndpointReadNeon extends StorageFileReader
{
    protected string $extension = 'neon';

    /**
     * get data and fill it into the pipeline
     *
     * @throws EndpointCodeException
     * @throws DataReadException
     */
    public function fillPipeline(PipelineData &$pipelineData): void
    {
        try {
            $fileContent = $this->storageGet($this->path);
            $content = (new Processor)->process(
                Expect::arrayOf('array'),
                Neon::decode($fileContent)
            );
        } catch (ValidationException|NeonException $e) {
            throw new EndpointCodeException(class_basename($this), $e->getMessage());
        }
        // from Neon processor parsing we always get an array
        $idName = $pipelineData->sheetBaseSchema->getIdName();
        foreach ($content as $index => $row) {
            //            if (! isset($row[$idName])) {
            //                $row[$idName] = $index;
            //            }
            $pipelineData->addRow($row);
        }
    }
}
