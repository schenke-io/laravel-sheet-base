<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Commands;

use Illuminate\Support\Facades\Config;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Tests\TestCase;

class LaravelSheetBaseCommandTest extends TestCase
{
    //    public function setUp(): void
    //    {
    //        parent::setUp();
    //
    //        // This fixed the issue
    //        $this->withoutMockingConsoleOutput();
    //    }
    public function testNoErrors(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', []);
        $this->artisan('sheet-base:check')->assertExitCode(0);
        $this->artisan('sheet-base:check')->assertOk();
    }

    public function testDiskError(): void
    {
        Config::set('filesystems.disks.sheet-base', null);
        $this->artisan('sheet-base:check')->assertExitCode(1);
        $this->artisan('sheet-base:check')->assertFailed();

    }

    public function testConfigError(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE, null);
        $this->artisan('sheet-base:check')->assertExitCode(1);
        $this->artisan('sheet-base:check')->assertFailed();
    }

    public function testSyntaxError(): void
    {
        Config::set('filesystems.disks.sheet-base', []);
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', [
            'pipeline1' => ['sources' => ['dummy']],
        ]);
        $this->artisan('sheet-base:check')->assertExitCode(1);
        $this->artisan('sheet-base:check')->assertFailed();
    }
}
