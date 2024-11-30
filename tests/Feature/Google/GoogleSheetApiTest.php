<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Google;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Exception as Google_Service_Exception;
use Google\Service\Sheets;
use Google\Service\Sheets\Resource\SpreadsheetsValues;
use Google\Service\Sheets\UpdateValuesResponse;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Illuminate\Support\Facades\Http;
use Mockery;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\MockObject\Exception;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleServiceException;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;

class GoogleSheetApiTest extends TestCase
{
    /*
         * ========================================================================
         *
         *      constructor()
         *
         * ========================================================================
         */
    public function test_constructor()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('useApplicationDefaultCredentials')->once();
        $mockClient->shouldReceive('addScope')->once()->with(Drive::DRIVE);

        $api = new GoogleSheetApi($mockClient);
        $this->assertInstanceOf(Sheets::class, $api->sheets);
    }

    /*
     * ========================================================================
     *
     *       getData()
     *
     * ========================================================================
     */
    public function test_get_data_ok()
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
        $api->sheets->spreadsheets_values = $mockValues;

        $this->assertEquals($data, $api->getData('spreadsheetId', 'sheetName'));
    }

    /**
     * @throws GoogleServiceException
     * @throws Exception
     */
    public function test_get_data_fail()
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
            ->willThrowException(new Google_Service_Exception(''));
        $this->expectException(GoogleServiceException::class);
        $api = new GoogleSheetApi;
        $api->sheets->spreadsheets_values = $mockValues;

        $this->assertEquals($data, $api->getData('spreadsheetId', 'sheetName'));
    }

    /*
         * ========================================================================
         *
         *       putData()
         *
         * ========================================================================
         */
    public function test_put_data_ok()
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
        $api->sheets->spreadsheets_values = $mockValues;
        $this->assertInstanceOf(UpdateValuesResponse::class, $api->putData('spreadsheetId', 'range', $data));
    }

    public function test_put_data_fail()
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
            )->willThrowException(new Google_Service_Exception(''));
        $this->expectException(GoogleServiceException::class);

        $api = new GoogleSheetApi;
        $api->sheets->spreadsheets_values = $mockValues;
        $this->assertInstanceOf(UpdateValuesResponse::class, $api->putData('spreadsheetId', 'range', $data));
    }

    /*
    * ========================================================================
    *
    *       putData()
    *
    * ========================================================================
    */

    /**
     * @throws Google_Service_Exception
     */
    public function test_batch_update_ok()
    {

        // Create a mock HTTP client
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([
                'values' => [],
            ], 200),
        ]);

        $spreadsheetId = 'your_spreadsheet_id';
        $api = new GoogleSheetApi;
        $requests = [
            // ... your request array
        ];

        // Mock the Sheets service
        $mockSheets = $this->mock('Google\Service\Sheets');

        $mockSheets->shouldReceive('spreadsheets')->andReturnSelf();
        $mockSheets->shouldReceive('batchUpdate')->with($spreadsheetId, Mockery::type(Google_Service_Sheets_BatchUpdateSpreadsheetRequest::class));

        $api->sheets->spreadsheets = $mockSheets;

        // Call the method to be tested
        $api->batchUpdate($spreadsheetId, $requests);
        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws GoogleServiceException
     */
    public function test_batch_update_fail()
    {

        // Create a mock HTTP client
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([
                'values' => [],
            ], 200),
        ]);

        $spreadsheetId = 'your_spreadsheet_id';
        $api = new GoogleSheetApi;
        $requests = [
            // ... your request array
        ];

        // Mock the Sheets service
        $mockSheets = $this->mock('Google\Service\Sheets');

        $mockSheets->shouldReceive('spreadsheets')->andReturnSelf();
        $mockSheets->shouldReceive('batchUpdate')
            ->with($spreadsheetId, Mockery::type(Google_Service_Sheets_BatchUpdateSpreadsheetRequest::class))
            ->andThrow(new Google_Service_Exception(''));
        $this->expectException(GoogleServiceException::class);
        $api->sheets->spreadsheets = $mockSheets;

        // Call the method to be tested
        $api->batchUpdate($spreadsheetId, $requests);

    }

    /*
* ========================================================================
*
*       getSheetId()
*
* ========================================================================
*/

    /**
     * @throws Google_Service_Exception
     */
    public function test_get_sheet_id_ok()
    {
        $data = [
            'a' => 1,
        ];

        // Create a mock HTTP client
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([
                'values' => $data,
            ], 200),
        ]);

        $spreadsheetId = 54321;
        $sheetId = 300;
        $api = new GoogleSheetApi;

        // Mock the Google Sheets API client
        $mockSheets = $this->mock('Google\Service\Sheets');
        $mockSpreadsheet = $this->mock('Google\Service\Sheets\Spreadsheet');
        $mockSheet1 = $this->mock('Google\Service\Sheets\Sheet');
        $mockSheet2 = $this->mock('Google\Service\Sheets\Sheet');

        // Set up expectations
        $mockSheets->shouldReceive('spreadsheets')->andReturnSelf();
        $mockSheets->shouldReceive('get')->with($spreadsheetId)->andReturn($mockSpreadsheet);

        // Test case 1: Sheet found
        $mockSpreadsheet->shouldReceive('getSheets')->andReturn([$mockSheet1, $mockSheet2]);
        $mockSheet1->shouldReceive('getProperties')->andReturnSelf();
        $mockSheet1->shouldReceive('getTitle')->andReturn('Sheet1');
        $mockSheet1->shouldReceive('getSheetId')->andReturn($sheetId);
        $mockSheet2->shouldReceive('getProperties')->andReturnSelf();
        $mockSheet2->shouldReceive('getTitle')->andReturn('Sheet2');

        $api->sheets->spreadsheets = $mockSheets;
        $sheetId = $api->getSheetId($spreadsheetId, 'Sheet1');
        $this->assertEquals($sheetId, $api->getSheetId($spreadsheetId, 'Sheet1'));

        // Test case 2: Sheet not found
        $mockSpreadsheet->shouldReceive('getSheets')->andReturn([$mockSheet1, $mockSheet2]);
        $mockSheet1->shouldReceive('getProperties')->andReturnSelf();
        $mockSheet1->shouldReceive('getTitle')->andReturn('Sheet1');
        $mockSheet2->shouldReceive('getProperties')->andReturnSelf();
        $mockSheet2->shouldReceive('getTitle')->andReturn('Sheet2');

        $this->assertEquals(-1, $api->getSheetId($spreadsheetId, 'Sheet3'));

        // Test case 3: Empty sheet list
        $mockSpreadsheet->shouldReceive('getSheets')->andReturn([]);
        $this->assertEquals(-1, $api->getSheetId($spreadsheetId, 'Sheet9'));

    }

    public function test_get_sheet_id_fail()
    {
        $data = [
            'a' => 1,
        ];

        // Create a mock HTTP client
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([
                'values' => $data,
            ], 200),
        ]);

        $spreadsheetId = 54321;
        $sheetId = 300;
        $api = new GoogleSheetApi;

        // Mock the Google Sheets API client
        $mockSheets = $this->mock('Google\Service\Sheets');

        // Set up expectations
        $mockSheets->shouldReceive('spreadsheets')->andReturnSelf();
        $mockSheets->shouldReceive('get')->with($spreadsheetId)
            ->andThrow(new Google_Service_Exception(''));

        $this->expectException(GoogleServiceException::class);
        $api->sheets->spreadsheets = $mockSheets;
        $sheetId = $api->getSheetId($spreadsheetId, 'Sheet1');
    }
}
