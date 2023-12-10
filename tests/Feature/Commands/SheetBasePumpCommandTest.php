<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Commands;

use Illuminate\Support\Facades\Config;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Tests\data\DummyRead;
use SchenkeIo\LaravelSheetBase\Tests\data\DummySchema;
use SchenkeIo\LaravelSheetBase\Tests\data\DummyWrite;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class SheetBasePumpCommandTest extends ConfigTestCase
{
    public function testNoErrors(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', [
            'test' => [
                'sources' => [DummyRead::class],
                'schema' => DummySchema::class,
                'target' => DummyWrite::class,
            ],
        ]);
        $this->artisan('sheet-base:pump')->assertOk();
    }
}
