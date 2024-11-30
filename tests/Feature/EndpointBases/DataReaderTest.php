<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\EndpointBases\DataReader;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

class DataReaderTest extends TestCase
{
    /**
     * @throws EndpointCodeException
     */
    public function test_can_fill_pipeline()
    {
        // define schema
        $sheetBaseSchema = new class extends SheetBaseSchema
        {
            /**
             * define the schema in Laravel migration syntax
             */
            protected function define(): void
            {
                $this->addId('a');
                $this->addString('b');
            }
        };
        // define pipeline
        $pipelineData = new PipelineData($sheetBaseSchema);
        // define reader
        $dataReader = new class extends DataReader
        {
            /**
             * get an array of rows to be filled into the pipeline
             */
            public function getArray(): array
            {
                return [
                    ['a' => 1, 'b' => 'alpha'],
                    ['a' => 2, 'b' => 'beta'],
                ];
            }

            /**
             * brief text what this endpoint is doing
             */
            public function explain(): string
            {
                return 'in test';
            }
        };
        $this->assertEquals([], $pipelineData->toArray());
        $dataReader->fillPipeline($pipelineData);
        $this->assertEquals([
            1 => ['b' => 'alpha'],
            2 => ['b' => 'beta'],
        ], $pipelineData->toArray());

    }
}
