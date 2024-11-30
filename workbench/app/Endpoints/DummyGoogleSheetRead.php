<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadGoogleSheet;

class DummyGoogleSheetRead extends EndpointReadGoogleSheet
{
    public string $spreadsheetId = 'abcdabcdabcd';

    public string $sheetName = 'Sheet1';
}
