<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Google;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadGoogleSheet;

class DummyGoogleSheetBaseRead extends EndpointReadGoogleSheet
{
    public string $spreadsheetId = 's3424242';

    public string $sheetName = 'Sheet123';
}
