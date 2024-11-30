<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;

class EndpointWriteArray implements IsWriter
{
    public array $arrayData = [];

    /**
     * this method needs to be overwritten
     */
    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $this->arrayData = $pipelineData->toArray();
        /*
         * here the data could be e.g. written into a database or to an api
         */
        //        foreach($pipelineData->toArray() as $key => $value) {
        //
        //        }
    }

    public function explain(): string
    {
        return 'writes into public array $arrayData';
    }

    public function toString(): string
    {

        return '$this->arrayData';
    }
}
