<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\EndpointBases\GoogleSheetBase;

class GoogleSheetBaseTest extends TestCase
{
    public function testSpreadsheetIdFromConfig()
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

    public function testSpreadsheetIdErrorFromConfig()
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
}
