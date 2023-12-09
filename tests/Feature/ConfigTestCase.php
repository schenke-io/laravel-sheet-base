<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;

class ConfigTestCase extends TestCase
{
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
                        'root' => realpath(__DIR__.'/../data'),
                    ],
                    'sheet-base' => [
                        'driver' => 'local',
                        'root' => __DIR__.'/../data',
                    ],
                    'default' => 'testing',
                ],
            ]
        );
    }
}
