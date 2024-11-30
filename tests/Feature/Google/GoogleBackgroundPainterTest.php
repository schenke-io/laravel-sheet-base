<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Google;

use Google\Service\Exception as Google_Service_Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Mockery;
use SchenkeIo\LaravelSheetBase\Exceptions\GoogleServiceException;
use SchenkeIo\LaravelSheetBase\Google\GoogleBackgroundPainter;
use SchenkeIo\LaravelSheetBase\Google\GoogleSheetApi;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class GoogleBackgroundPainterTest extends ConfigTestCase
{
    public function test_make(): void
    {
        $painter = GoogleBackgroundPainter::take(new Command, 'name', new DummyGoogleSheetBaseRead);
        $this->assertInstanceOf(GoogleBackgroundPainter::class, $painter);
    }

    /**
     * @throws GoogleServiceException
     */
    public function test_mark_red_ok_empty()
    {
        $sheet = Mockery::mock(DummyGoogleSheetBaseRead::class);
        $api = Mockery::mock(GoogleSheetApi::class);
        $cmd = Mockery::mock(Command::class);

        $sheet->googleSheetApi = $api;

        $painter = GoogleBackgroundPainter::take($cmd, 'name', $sheet);

        $painter->markRed([]);
        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws GoogleServiceException
     */
    public function test_mark_red_ok_keys()
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
        $api->shouldReceive('getSheetId')->once()->andReturn(1234);
        $api->shouldReceive('batchUpdate')->once();

        $cmd = Mockery::mock(Command::class);
        $cmd->shouldReceive('info')->twice();

        $sheet->googleSheetApi = $api;

        $painter = GoogleBackgroundPainter::take($cmd, 'name', $sheet);

        $painter->markRed(['a', 'b']);
    }

    public function test_mark_red_fails()
    {
        Http::fake([
            'https://sheets.googleapis.com/v4/spreadsheets/*' => Http::response([
                'values' => [],
            ], 500),
        ]);
        $sheet = new DummyGoogleSheetBaseRead;

        $api = Mockery::mock(GoogleSheetApi::class);
        $api->shouldReceive('getData')->once();
        $api->shouldReceive('getSheetId')->once()->andReturn(1234);
        $api->shouldReceive('batchUpdate')->once()->andThrow(new Google_Service_Exception(''));

        $cmd = Mockery::mock(Command::class);
        $cmd->shouldReceive('info')->once();
        $cmd->shouldReceive('error')->once();

        $sheet->googleSheetApi = $api;

        $painter = GoogleBackgroundPainter::take($cmd, 'name', $sheet);

        $painter->markRed(['a', 'b']);
    }
}
