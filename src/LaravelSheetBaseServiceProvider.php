<?php

namespace SchenkeIo\LaravelSheetBase;

use SchenkeIo\LaravelSheetBase\Console\Commands\SheetBaseCheckCommand;
use SchenkeIo\LaravelSheetBase\Console\Commands\SheetBasePumpCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSheetBaseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-sheet-base')
            ->hasCommand(SheetBasePumpCommand::class)
            ->hasCommand(SheetBaseCheckCommand::class)
            ->hasConfigFile('sheet-base')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishConfigFile();
            });
    }
}
