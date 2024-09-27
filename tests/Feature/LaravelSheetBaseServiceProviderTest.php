<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature;

use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\LaravelSheetBaseServiceProvider;
use Spatie\LaravelPackageTools\Package;

class LaravelSheetBaseServiceProviderTest extends TestCase
{
    public function testConfigurePackage()
    {
        $serviceProvider = new LaravelSheetBaseServiceProvider($this->app);
        $package = new Package;
        $serviceProvider->configurePackage($package);
        $serviceProvider->register();
        $serviceProvider->boot();
        $this->assertEquals('laravel-sheet-base', $package->name);
        $this->assertArrayHasKey('sheet-base-config', $serviceProvider::$publishGroups);
        $this->assertCount(2, $package->commands);
    }
}
