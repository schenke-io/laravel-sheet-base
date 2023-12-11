<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints;

use Nette\Neon\Exception as NeonException;
use Nette\Neon\Neon;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReader;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

class EndpointReadNeon extends StorageFileReader
{
    protected string $extension = 'neon';

    /**
     * get data and fill it into the pipeline
     *
     * @throws ReadParseException
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
            throw new ReadParseException($e->getMessage());
        }
        // from Neon processor parsing we always get an array
        foreach ($content as $row) {
            $pipelineData->addRow($row);
        }
    }
}
