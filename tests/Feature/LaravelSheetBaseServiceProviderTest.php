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
        $package = new Package();
        $serviceProvider->configurePackage($package);
        $this->assertEquals('laravel-sheet-base', $package->name);
        $this->assertEquals(1, count($package->configFileNames));
        $this->assertEquals(1, count($package->commands));

    }
}
