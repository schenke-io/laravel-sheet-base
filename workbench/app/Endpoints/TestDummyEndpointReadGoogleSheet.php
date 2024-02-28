<?php

namespace Workbench\App\Endpoints;

use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadGoogleSheet;

class TestDummyEndpointReadGoogleSheet extends EndpointReadGoogleSheet
{
    public function __construct(string $spreadsheetId, string $sheetName)
    {
        $this->spreadsheetId = $spreadsheetId;
        $this->sheetName = $sheetName;
        parent::__construct();
    }
}
