<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use SchenkeIo\LaravelSheetBase\LaravelSheetBaseServiceProvider;

class ConfigTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [LaravelSheetBaseServiceProvider::class];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('filesystems',
            [
                'disks' => [
                    'testing' => [
                        'driver' => 'local',
                        'root' => realpath(__DIR__.'/../../workbench'),
                    ],
                    'sheet-base' => [
                        'driver' => 'local',
                        'root' => realpath(__DIR__.'/../../workbench/resources'),
                    ],
                    'default' => 'testing',
                ],
            ]
        );

    }
}
