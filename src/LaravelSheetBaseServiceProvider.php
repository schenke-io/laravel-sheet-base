<?php

namespace SchenkeIo\LaravelSheetBase;

use SchenkeIo\LaravelSheetBase\Commands\LaravelSheetBaseCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSheetBaseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {

        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-sheet-base')
            ->hasConfigFile(['sheet-base'])
            ->hasCommand(LaravelSheetBaseCommand::class);
    }
}
