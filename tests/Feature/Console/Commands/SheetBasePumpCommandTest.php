<?php

namespace Feature\Console\Commands;

use Illuminate\Support\Facades\Config;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummyRead;
use Workbench\App\Endpoints\DummySchema;
use Workbench\App\Endpoints\DummyWrite;

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

    public function testErrors(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', [
            'test' => [
                'sources' => [''],
                'schema' => DummySchema::class,
                'target' => DummyWrite::class,
            ],
        ]);
        $this->artisan('sheet-base:pump')->assertExitCode(1);
    }
}
