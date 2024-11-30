<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use Google\Service\Sheets;
use Google\Service\Sheets\Resource\SpreadsheetsValues;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\MockObject\Exception;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleSheetException;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;
use Workbench\App\Endpoints\TestDummyEndpointReadGoogleSheet;

class EndpointReadGoogleSheetTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Google\Service\Exception
     * @throws DataReadException
     */
    public function test_fill_pipeline()
    {
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addId('a');
                $this->addUnsigned('b');
            }
        };

        $mockValues = $this->createMock(SpreadsheetsValues::class);
        $mockValues->method('get')
            ->with(
                'spreadsheetId',
                'sheetName'
            )
            ->willReturn(new Sheets\ValueRange(['values' => [
                ['a', 'b', '', 'c', 'd'], // only first 2 are used
                [1, 2],
                [2, 3],
                [null, null],  // this line ends reading
                [3, 5],
            ],
            ]
            )
            );

        $api = new GoogleSheetApi;
        $api->sheets->spreadsheets_values = $mockValues;

        $pipelineData = new PipelineData($schema);
        $sheet = new TestDummyEndpointReadGoogleSheet('spreadsheetId', 'sheetName');
        $sheet->googleSheetApi = $api;

        $sheet->fillPipeline($pipelineData);
        $result = $pipelineData->toArray();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function test_explain()
    {
        $sheet = new TestDummyEndpointReadGoogleSheet('spreadsheetId', 'sheetName');
        $this->assertGreaterThan(2, strlen($sheet->explain()));
    }

    public function test_exception_spreadsheet_id()
    {
        $this->expectException(GoogleSheetException::class);
        $sheet = new TestDummyEndpointReadGoogleSheet('', 'sheetName');
    }

    public function test_exception_sheet_name()
    {
        $this->expectException(GoogleSheetException::class);
        $sheet = new TestDummyEndpointReadGoogleSheet('spreadsheetId', '');
    }
}
