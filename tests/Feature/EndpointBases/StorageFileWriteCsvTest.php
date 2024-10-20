<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileWriteCsv;
use SchenkeIo\LaravelSheetBase\Exceptions\DataQualityException;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers\DummySheetBaseSchema;

class StorageFileWriteCsvTest extends ConfigTestCase
{
    public function testStoragePut(): void
    {
        $path = '/testfile.psv';
        $content = "a|b\n1|2\n";
        Storage::fake('sheet-base');

        $file = new class extends StorageFileWriteCsv
        {
            public string $path = '/testfile.psv';

            protected string $extension = 'psv';

            protected string $delimiter = '|';

            public function publicStoragePut(string $path, string $content): void
            {
                $this->storagePut($path, $content);
            }
        };
        $file->publicStoragePut($path, $content);
        $this->assertEquals($content, Storage::disk('sheet-base')->get($path));
    }

    /**
     * @throws DataReadException
     * @throws DataQualityException
     */
    public function testWriteFromPipelineOk()
    {
        $path = '/testfile.psv';
        $pipeline = new PipelineData(new DummySheetBaseSchema);
        $pipeline->addRow(['a' => 1, 'b' => 2]);
        $pipeline->addRow(['a' => 2, 'b' => 3]);
        $content = "a|b\n1|2\n2|3\n";

        Storage::fake('sheet-base');
        $file = new class($path) extends StorageFileWriteCsv
        {
            protected string $extension = 'psv';

            protected string $delimiter = '|';
        };
        $file->releasePipeline($pipeline, '');
        $this->assertEquals($content, Storage::disk('sheet-base')->get($path));
    }

    public function testReadFromPipelineDelimiterInData()
    {
        $path = '/testfile.psv';
        $pipeline = new PipelineData(new DummySheetBaseSchema);
        $pipeline->addRow(['a' => 1, 'b' => 'a|b']);
        $pipeline->addRow(['a' => 2, 'b' => 3]);
        $this->expectException(DataQualityException::class);

        Storage::fake('sheet-base');
        $file = new class($path) extends StorageFileWriteCsv
        {
            protected string $extension = 'psv';

            protected string $delimiter = '|';
        };
        $file->releasePipeline($pipeline, '');
    }

    public function testDelimiterMissing()
    {
        $path = '/testfile.psv';
        $this->expectException(EndpointCodeException::class);

        Storage::fake('sheet-base');
        $file = new class($path) extends StorageFileWriteCsv
        {
            protected string $extension = 'psv';
        };

    }
}