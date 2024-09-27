<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Google;

use Google\Service\Sheets;
use Google\Service\Sheets\Resource\SpreadsheetsValues;
use Google\Service\Sheets\UpdateValuesResponse;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\MockObject\Exception;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;

class GoogleSheetApiTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testGetData()
    {
        $data = [
            ['data1', 'data2'],
            ['data3', 'data4'],
        ];

        // Create a mock HTTP client
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([
                'values' => $data,
            ], 200),
        ]);

        $mockValues = $this->createMock(SpreadsheetsValues::class);
        $mockValues->method('get')
            ->with(
                'spreadsheetId',
                'sheetName'
            )
            ->willReturn(new Sheets\ValueRange(['values' => $data]));

        $api = new GoogleSheetApi;
        $api->spreadsheetsValues = $mockValues;

        $this->assertEquals($data, $api->getData('spreadsheetId', 'sheetName'));
    }

    /**
     * @throws Exception
     * @throws \Google\Service\Exception
     */
    public function testWriteData()
    {
        $data = [
            ['data1', 'data2'],
            ['data3', 'data4'],
        ];
        // Create a mock HTTP client
        Http::fake(['https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([], 200)]);

        $mockValues = $this->createMock(SpreadsheetsValues::class);
        $mockValues->method('update')
            ->with(
                'spreadsheetId', 'range',
                new Sheets\ValueRange([
                    'majorDimension' => 'ROWS',
                    'values' => [$data],
                ]),
                ['valueInputOption' => 'RAW']
            )
            ->willReturn(new UpdateValuesResponse);
        $api = new GoogleSheetApi;
        $api->spreadsheetsValues = $mockValues;
        $this->assertInstanceOf(UpdateValuesResponse::class, $api->putData('spreadsheetId', 'range', $data));
    }
}
