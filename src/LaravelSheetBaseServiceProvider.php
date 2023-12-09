<?php

namespace SchenkeIo\LaravelSheetBase;

use SchenkeIo\LaravelSheetBase\Commands\LaravelSheetBaseCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSheetBaseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $this->publishes(
            [
                __DIR__.'/../config/sheet-base.php' => config_path('sheet-base.php'),
            ],
            'laravel-sheet-base-config'
        );
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-sheet-base')
            ->hasCommand(LaravelSheetBaseCommand::class);
    }
}
