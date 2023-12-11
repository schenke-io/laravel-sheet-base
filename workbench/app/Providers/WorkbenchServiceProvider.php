<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use SchenkeIo\LaravelSheetBase\Console\Commands\SheetBaseCheckCommand;
use SchenkeIo\LaravelSheetBase\Console\Commands\SheetBasePumpCommand;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //        $this->commands([
        //            SheetBaseCheckCommand::class,
        //            SheetBasePumpCommand::class,
        //        ]);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->commands([
            SheetBaseCheckCommand::class,
            SheetBasePumpCommand::class,
        ]);
        $this->mergeConfigFrom(__DIR__.'/../../config/filesystems.php', 'filesystems');
    }
}
