<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Writers;

use Mockery;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteGoogleSheet;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;
use SchenkeIo\LaravelSheetBase\Tests\TestCase;

class EndpointWriteGoogleSheetTest extends TestCase
{
    public function test_release_pipeline_writes_to_google_sheet(): void
    {
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addId('id');
                $this->addString('name');
            }
        };

        $pipelineData = new PipelineData($schema);
        $pipelineData->addRow(['id' => '1', 'name' => 'Alice']);
        $pipelineData->addRow(['id' => '2', 'name' => 'Bob']);

        $mockApi = Mockery::mock(GoogleSheetApi::class);
        $mockApi->shouldReceive('putData')
            ->once()
            ->with(
                'spreadsheet-id',
                'Sheet1',
                [
                    ['id', 'name'],
                    ['1', 'Alice'],
                    ['2', 'Bob'],
                ]
            );

        /** @var EndpointWriteGoogleSheet $endpoint */
        $endpoint = Mockery::mock(EndpointWriteGoogleSheet::class)->makePartial();
        $endpoint->googleSheetApi = $mockApi;
        $endpoint->spreadsheetId = 'spreadsheet-id';
        $endpoint->sheetName = 'Sheet1';

        $endpoint->releasePipeline($pipelineData, 'SomeClass');

        $this->assertTrue(true); // assert something to avoid warning
    }

    public function test_explain_returns_correct_string(): void
    {
        /** @var EndpointWriteGoogleSheet $endpoint */
        $endpoint = Mockery::mock(EndpointWriteGoogleSheet::class)->makePartial();
        $endpoint->spreadsheetId = 'spreadsheet-id';
        $endpoint->sheetName = 'Sheet1';

        $this->assertEquals(
            "writes into Google Sheet 'Sheet1' in spreadsheet 'spreadsheet-id'",
            $endpoint->explain()
        );
    }
}
