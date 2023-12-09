<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Tests\TestCase;

class PipelineDataTest extends TestCase
{
    protected SheetBaseSchema $sheetBaseSchemaTable;

    protected SheetBaseSchema $sheetBaseSchemaTree;

    protected array $dataTable = [
        0 => ['id' => 1, 'c1' => 'a'],
        1 => ['id' => 2, 'c1' => 'b'],
        2 => ['id' => 1, 'c1' => 'c'],
    ];

    protected array $dataTree = [
        0 => ['id' => 'test.title', 'de' => 'Hallo Welt', 'en' => 'Hello world'],
        1 => ['id' => 'test.story', 'de' => 'Alle Details', 'en' => 'All details'],
        2 => ['id' => 'test.help', 'de' => 'Rufen Sie an', 'en' => 'Call us'],
    ];

    protected function setUp(): void
    {
        $this->sheetBaseSchemaTable = new class extends SheetBaseSchema
        {
            /**
             * define the schema in Laravel migration syntax
             */
            protected function define(): void
            {
                $this->addId();
                $this->addString('c1');
                $this->addString('c2');
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
     * @throws ReadParseException
     */
    public function testOverwriteOfPipelineData(): void
    {
        $pipeline = new PipelineData($this->sheetBaseSchemaTable);
        $pipeline->addRow($this->dataTable[0]);
        $pipeline->addRow($this->dataTable[1]);
        $this->assertEquals('a', $pipeline->toArray()[1]['c1'], 'data loaded');
        $this->assertEquals(null, $pipeline->toArray()[1]['c2'], 'empty data is null');
        $pipeline->addRow($this->dataTable[2]);
        $this->assertEquals('c', $pipeline->toArray()[1]['c1'], 'data overwritten');

    }

    public function testFromArray(): void
    {
        $pipelineData = PipelineData::fromArray($this->dataTable, $this->sheetBaseSchemaTable);
        $this->assertEquals($this->dataTable, $pipelineData->toArray());

        $pipelineData = PipelineData::fromArray($this->dataTree, $this->sheetBaseSchemaTree);
        $this->assertEquals($this->dataTree, $pipelineData->toArray());
    }

    public function testAddRowEmptyIdException()
    {
        $this->expectException(ReadParseException::class);
        $pipeline = new PipelineData($this->sheetBaseSchemaTable);
        $pipeline->addRow(['id' => '', 'c1' => 'text']);
    }

    /**
     * @throws ReadParseException
     */
    public function testAddRowTree()
    {
        $pipelineData = new PipelineData($this->sheetBaseSchemaTree);
        $pipelineData->addRow($this->dataTree[0]);
        $pipelineData->addRow($this->dataTree[1]);
        $pipelineData->addRow($this->dataTree[2]);
        $this->assertArrayHasKey('test', $pipelineData->toArray());
    }
}
