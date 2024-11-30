<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Mockery;
use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\EndpointBases\GoogleSheetBase;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;
use SchenkeIo\LaravelSheetBase\Tests\Feature\Google\DummyGoogleSheetBaseRead;

class GoogleSheetBaseTest extends TestCase
{
    public function test_get_all()
    {
        $data = [['a'], ['b'], ['c']];
        // Create a mock HTTP client
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([
                'values' => $data,
            ], 200),
        ]);
        $sheet = new DummyGoogleSheetBaseRead;

        $api = Mockery::mock(GoogleSheetApi::class);
        $api->shouldReceive('getData')->once()->andReturn($data);

        $sheet->googleSheetApi = $api;
        $sheet->get();
    }

    public function test_spreadsheet_id_from_config()
    {
        Config::set('sheet-base.spreadsheets.test1', 'id1');
        $class = new class extends GoogleSheetBase
        {
            public string $spreadsheetId = 'test1';

            public string $sheetName = 'sheet1';

            public function explain(): string
            {
                return 'test';
            }
        };
        $this->assertEquals('id1', $class->spreadsheetId);
    }

    public function test_spreadsheet_id_error_from_config()
    {
        Config::set('sheet-base.spreadsheets.test2', 'id1');
        $class = new class extends GoogleSheetBase
        {
            public string $spreadsheetId = 'test1';

            public string $sheetName = 'sheet1';

            public function explain(): string
            {
                return 'test';
            }
        };
        $this->assertEquals('test1', $class->spreadsheetId);
    }

    public function test_to_string(): void
    {
        Config::set('sheet-base.spreadsheets.test2', 'id1');
        $class = new class extends GoogleSheetBase
        {
            public string $spreadsheetId = 'test1';

            public string $sheetName = 'sheet1';

            public function explain(): string
            {
                return 'test';
            }
        };
        $this->assertIsString($class->toString());
    }
}
