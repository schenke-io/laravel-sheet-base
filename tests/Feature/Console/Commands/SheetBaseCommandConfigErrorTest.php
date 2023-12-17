<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Config;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummySchema;
use Workbench\App\Endpoints\DummyWrite;

class SheetBaseCommandConfigErrorTest extends ConfigTestCase
{
    public function testDiskError(): void
    {
        Config::set('filesystems.disks.sheet-base', null);
        $this->artisan('sheet-base:check')->assertExitCode(1);
        $this->artisan('sheet-base:pump')->assertExitCode(0);
    }

    public function testConfigError(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE, null);
        $this->artisan('sheet-base:check')->assertExitCode(1);
        $this->artisan('sheet-base:pump')->assertExitCode(0);
    }

    public function testSyntaxError(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', [
            'pipeline1' => ['sources' => ['dummy']],
        ]);
        $this->artisan('sheet-base:check')->assertExitCode(1);
        $this->artisan('sheet-base:pump')->assertExitCode(1);
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
        $this->artisan('sheet-base:check')->assertExitCode(1);
        $this->artisan('sheet-base:pump')->assertExitCode(1);
    }
}
