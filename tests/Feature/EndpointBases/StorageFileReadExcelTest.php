<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReadExcel;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\PersonSchema;

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

    public function test_multi_line_data()
    {
        $path = 'psv/multiline.psv';
        $endpoint = new class($path) extends StorageFileReadExcel
        {
            protected string $extension = 'psv';

            protected string $delimiter = '|';
        };
        $content = [
            1 => ['name' => 'yes'],
            2 => ['name' => 'line1'.PHP_EOL.'line2'.PHP_EOL.'|'],
        ];
        $pipelineData = new PipelineData(new PersonSchema);
        $endpoint->fillPipeline($pipelineData);

        $this->assertEquals($content, $pipelineData->toArray());
    }
}
