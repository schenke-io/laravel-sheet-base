<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use SchenkeIo\LaravelSheetBase\Commands\LaravelSheetBaseCommand;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //        parent::boot();
        $this->commands([
            LaravelSheetBaseCommand::class,
        ]);
    }
}
