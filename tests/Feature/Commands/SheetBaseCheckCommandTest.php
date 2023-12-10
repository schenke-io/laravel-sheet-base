<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Commands;

use Illuminate\Support\Facades\Config;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Tests\data\DummyRead;
use SchenkeIo\LaravelSheetBase\Tests\data\DummySchema;
use SchenkeIo\LaravelSheetBase\Tests\data\DummyWrite;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class SheetBaseCheckCommandTest extends ConfigTestCase
{
    public function testNoErrors(): void
    {
        Config::set('filesystems.disks.sheet-base', [
            'driver' => 'local',
            'root' => 'test root location',
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

    public function testDiskError(): void
    {
        Config::set('filesystems.disks.sheet-base', null);
        $this->artisan('sheet-base:check')->assertExitCode(1);
    }

    public function testConfigError(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE, null);
        $this->artisan('sheet-base:check')->assertExitCode(1);
    }

    public function testSyntaxError(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', [
            'pipeline1' => ['sources' => ['dummy']],
        ]);
        $this->artisan('sheet-base:check')->assertExitCode(1);
    }
}
