<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use Illuminate\Console\Command;
use Mockery;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\PipelineType;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadArray;
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
            protected function define(): void
            {
                $this->addString('c1');
            }
        };
        $this->sheetBaseSchemaTree = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addDot();
                $this->addLanguage('de');
                $this->addLanguage('en');
            }
        };
    }

    public function test_overwrite_of_pipeline_data(): void
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

    public function test_overwrite_only_some_columns(): void
    {
        $pipeline = new PipelineData($this->sheetBaseSchemaTable1);
        $pipeline->addRow($this->dataTable[2]);
        $pipeline->addRow($this->dataTable[3]);
        $pipeline->addRow($this->dataTable[4]);
        $this->assertEquals('d', $pipeline->toArray()[1]['c1'], '2x overwritten');
        $this->assertEquals('e', $pipeline->toArray()[1]['c2'], 'do not destroy line before');
    }

    public function test_from_array(): void
    {
        $pipelineData = PipelineData::fromArray($this->dataTable, $this->sheetBaseSchemaTable1);
        $this->assertEquals($this->dataTable, $pipelineData->toArray());

        $pipelineData = PipelineData::fromArray($this->dataTree, $this->sheetBaseSchemaTree);
        $this->assertEquals($this->dataTree, $pipelineData->toArray());
    }

    public function test_add_row_empty_id_exception()
    {
        $pipeline = new PipelineData($this->sheetBaseSchemaTable1);
        $this->assertCount(0, $pipeline->toArray());
        $pipeline->addRow(['id' => '12', 'c1' => 'text 12']);
        $this->assertCount(1, $pipeline->toArray());
        $pipeline->addRow(['id' => '', 'c1' => 'text']);
        $this->assertCount(1, $pipeline->toArray());
    }

    public function test_add_row_tree()
    {
        $pipelineData = new PipelineData($this->sheetBaseSchemaTree);
        $pipelineData->addRow($this->dataTree[0]);
        $pipelineData->addRow($this->dataTree[1]);
        $pipelineData->addRow($this->dataTree[2]);
        $this->assertArrayHasKey('test.title', $pipelineData->toArray());
    }

    public function test_overwrite_tree(): void
    {
        $pipelineData = new PipelineData($this->sheetBaseSchemaTree);
        $pipelineData->addRow($this->dataTree[2]);
        $pipelineData->addRow($this->dataTree[3]);
        $this->assertArrayHasKey('test.help', $pipelineData->toArray());
        $this->assertEquals('Call us again', $pipelineData->toArray()['test.help']['en']);
    }

    public function test_new_from_type(): void
    {
        $pipelineData = PipelineData::fromType(PipelineType::Table);
        $this->assertCount(0, $pipelineData->toArray());
    }

    /**
     * @throws EndpointCodeException
     */
    public function test_filter_keys_off(): void
    {
        $pipeline = new PipelineData($this->sheetBaseSchemaTable1);
        $pipeline->addRow($this->dataTable[0]);
        $pipeline->addRow($this->dataTable[1]);
        $this->assertCount(2, $pipeline->toArray());
        $filter = new class extends EndpointReadArray
        {
            public function getArray(): array
            {
                return [
                    ['id' => 1],
                ];
            }
        };
        $cmd = Mockery::mock(Command::class);
        $cmd->shouldReceive('info')->once();
        $pipeline->filterKeysOff($cmd, '', $filter);  // remove key 1
        $this->assertCount(1, $pipeline->toArray());
    }
}
