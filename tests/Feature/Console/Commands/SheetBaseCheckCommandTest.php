<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Config;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummyRead;
use Workbench\App\Endpoints\DummySchema;
use Workbench\App\Endpoints\DummyWrite;

class SheetBaseCheckCommandTest extends ConfigTestCase
{
    public function testNoErrors(): void
    {

        Config::set('filesystems.disks.sheet-base', [
            'driver' => 'local',
            'root' => '/',
        ]);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', [
            'pipeline1' => [
                'sources' => [DummyRead::class],
                'schema' => DummySchema::class,
                'target' => DummyWrite::class,
            ],
        ]);
        $this->artisan('sheet-base:check')->assertExitCode(0);
    }
}
