<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use Google\Service\Sheets;
use Google\Service\Sheets\Resource\SpreadsheetsValues;
use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleSheetException;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;
use Workbench\App\Endpoints\EndpointReadGoogleSheetDummy;

class EndpointReadGoogleSheetTest extends TestCase
{
    public function testFillPipeline()
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
            ->willReturn(new Sheets\ValueRange(['values' => [['a', 'b'], [1, 2], [2, 5]]]));

        $api = new GoogleSheetApi();
        $api->spreadsheetsValues = $mockValues;

        $pipelineData = new PipelineData($schema);
        $sheet = new EndpointReadGoogleSheetDummy('spreadsheetId', 'sheetName');
        $sheet->spreadsheet = $api;

        $sheet->fillPipeline($pipelineData);
        $this->assertIsArray($pipelineData->toArray());
    }

    public function testExplain()
    {
        $sheet = new EndpointReadGoogleSheetDummy('spreadsheetId', 'sheetName');
        $this->assertGreaterThan(2, strlen($sheet->explain()));
    }

    public function testExceptionSpreadsheetId()
    {
        $this->expectException(GoogleSheetException::class);
        $sheet = new EndpointReadGoogleSheetDummy('', 'sheetName');
    }

    public function testExceptionSheetName()
    {
        $this->expectException(GoogleSheetException::class);
        $sheet = new EndpointReadGoogleSheetDummy('spreadsheetId', '');
    }
}
