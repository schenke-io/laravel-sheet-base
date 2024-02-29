<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\TestCase;

class PipelineDataTest extends TestCase
{
    protected SheetBaseSchema $sheetBaseSchemaTable1;

    protected SheetBaseSchema $sheetBaseSchemaTable2;

    protected SheetBaseSchema $sheetBaseSchemaTree;

    protected array $dataTable = [
        0 => ['id' => 1, 'c1' => 'a'],
        1 => ['id' => 2, 'c1' => 'b'],
        2 => ['id' => 1, 'c1' => 'c'],
        3 => ['id' => 1, 'c1' => 'd'],
        4 => ['id' => 1, 'c2' => 'e'],
        5 => ['id' => 1, 'c1' => 'f', 'c2' => 'f'],
    ];

    protected array $dataTree = [
        0 => ['id' => 'test.title', 'de' => 'Hallo Welt', 'en' => 'Hello world'],
        1 => ['id' => 'test.story', 'de' => 'Alle Details', 'en' => 'All details'],
        2 => ['id' => 'test.help', 'de' => 'Rufen Sie an', 'en' => 'Call us'],
        3 => ['id' => 'test.help', 'en' => 'Call us again'],
    ];

    protected function setUp(): void
    {
        $this->sheetBaseSchemaTable1 = new class extends SheetBaseSchema
        {
            /**
             * define the schema in Laravel migration syntax
             */
            protected function define(): void
            {
                $this->addId();
                $this->addString('c1');
                $this->addString('c2');
                $this->addUnsignedNotNull('c3');
            }
        };
        $this->sheetBaseSchemaTable2 = new class extends SheetBaseSchema
        {
            /**
             * define the schema in Laravel migration syntax
             */
            protected function define(): void
            {
                $this->addString('c1');
            }
        };
        $this->sheetBaseSchemaTree = new class extends SheetBaseSchema
        {
            /**
             * define the schema in Laravel migration syntax
             */
            protected function define(): void
            {
                $this->addDot();
                $this->addLanguage('de');
                $this->addLanguage('en');
            }
        };
    }

    /**
     * @throws EndpointCodeException
     */
    public function testOverwriteOfPipelineData(): void
    {
        $pipeline = new PipelineData($this->sheetBaseSchemaTable1);
        $pipeline->addRow($this->dataTable[0]);
        $pipeline->addRow($this->dataTable[1]);
        $this->assertEquals('a', $pipeline->toArray()[1]['c1'], 'data loaded');
        $this->assertEquals(null, $pipeline->toArray()[1]['c2'], 'empty data is null');
        $pipeline->addRow($this->dataTable[2]);
        $this->assertEquals('c', $pipeline->toArray()[1]['c1'], 'data overwritten');
        /*
         * check not null fields         *
         */
        $this->assertSame(0, $pipeline->toArray()[1]['c3'], 'no input results in 0 not in null');
    }

    public function testOverwriteOnlySomeColumns(): void
    {
        $pipeline = new PipelineData($this->sheetBaseSchemaTable1);
        $pipeline->addRow($this->dataTable[2]);
        $pipeline->addRow($this->dataTable[3]);
        $pipeline->addRow($this->dataTable[4]);
        $this->assertEquals('d', $pipeline->toArray()[1]['c1'], '2x overwritten');
        $this->assertEquals('e', $pipeline->toArray()[1]['c2'], 'do not destroy line before');
    }

    public function testFromArray(): void
    {
        $pipelineData = PipelineData::fromArray($this->dataTable, $this->sheetBaseSchemaTable1);
        $this->assertEquals($this->dataTable, $pipelineData->toArray());

        $pipelineData = PipelineData::fromArray($this->dataTree, $this->sheetBaseSchemaTree);
        $this->assertEquals($this->dataTree, $pipelineData->toArray());
    }

    public function testAddRowEmptyIdException()
    {
        $this->expectException(DataReadException::class);
        $pipeline = new PipelineData($this->sheetBaseSchemaTable1);
        $pipeline->addRow(['id' => '', 'c1' => 'text']);
    }

    /**
     * @throws EndpointCodeException
     */
    public function testAddRowTree()
    {
        $pipelineData = new PipelineData($this->sheetBaseSchemaTree);
        $pipelineData->addRow($this->dataTree[0]);
        $pipelineData->addRow($this->dataTree[1]);
        $pipelineData->addRow($this->dataTree[2]);
        $this->assertArrayHasKey('test', $pipelineData->toArray());
    }

    /**
     * @throws EndpointCodeException
     */
    public function testOverwriteTree(): void
    {
        $pipelineData = new PipelineData($this->sheetBaseSchemaTree);
        $pipelineData->addRow($this->dataTree[2]);
        $pipelineData->addRow($this->dataTree[3]);
        // dump($pipelineData->toArray());
        $this->assertArrayHasKey('test', $pipelineData->toArray());
        $this->assertEquals('Call us again', $pipelineData->toArray()['test']['help']['en']);
    }
}
