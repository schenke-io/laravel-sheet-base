<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReadExcel;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class StorageFileReadExcelTest extends ConfigTestCase
{
    protected const PATH = 'psv/persons.psv';

    public function test_seperator_missing()
    {
        $this->expectException(EndpointCodeException::class);
        $endpoint = new class(self::PATH) extends StorageFileReadExcel
        {
            protected string $extension = 'psv';
        };
    }

    public function test_path_missing()
    {
        $this->expectException(EndpointCodeException::class);
        $endpoint = new class extends StorageFileReadExcel
        {
            protected string $extension = 'csv';

            protected string $delimiter = ',';
        };
    }

    public function test_file_not_found()
    {
        $this->expectException(EndpointCodeException::class);
        $endpoint = new class('unknwon.csv') extends StorageFileReadExcel
        {
            protected string $extension = 'csv';

            protected string $delimiter = ',';
        };
    }

    public function test_all_right()
    {
        $path = 'psv/persons.psv';
        $endpoint = new class($path) extends StorageFileReadExcel
        {
            protected string $extension = 'psv';

            protected string $delimiter = ',';
        };
        $this->assertEquals($path, $endpoint->path);
    }
}
