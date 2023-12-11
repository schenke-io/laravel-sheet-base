<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use Illuminate\Support\Facades\Config;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\PersonSchema;
use Workbench\App\Endpoints\PersonsReadPsv;
use Workbench\App\Endpoints\PersonsWriteNeon;

class SheetBaseConfigTest extends ConfigTestCase
{
    public static function dataProvidedConfig(): array
    {
        return [
            'no error' => ['',
                [
                    'persons' => [
                        'sources' => [PersonsReadPsv::class],
                        'schema' => PersonSchema::class,
                        'target' => PersonsWriteNeon::class,
                    ],
                ],
            ],
            'no array in sources' => [ConfigErrorException::class,
                [
                    'persons' => [
                        'sources' => PersonsReadPsv::class,
                        'schema' => PersonSchema::class,
                        'target' => PersonsWriteNeon::class,
                    ],
                ],
            ],
            'wrong schema class' => [ConfigErrorException::class,
                [
                    'persons' => [
                        'sources' => [PersonsReadPsv::class],
                        'schema' => PersonsReadPsv::class,
                        'target' => PersonsWriteNeon::class,
                    ],
                ],
            ],
            'wrong source class' => [ConfigErrorException::class,
                [
                    'persons' => [
                        'sources' => [PersonSchema::class],
                        'schema' => PersonSchema::class,
                        'target' => PersonsWriteNeon::class,
                    ],
                ],
            ],
            'wrong target class' => [ConfigErrorException::class,
                [
                    'persons' => [
                        'sources' => [PersonsReadPsv::class],
                        'schema' => PersonSchema::class,
                        'target' => PersonSchema::class,
                    ],
                ],
            ],

        ];
    }

    /**
     * @dataProvider dataProvidedConfig
     *
     * @return void
     *
     * @throws ConfigErrorException
     */
    public function testConfigSyntax(string $exception, array $config)
    {
        if ($exception == '') {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException($exception);
        }
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', $config);
        SheetBaseConfig::make();
    }

    /**
     * @dataProvider dataProvidedConfig
     *
     * @return void
     */
    public function testCheckAndReportError(string $exception, array $config)
    {
        Config::set(SheetBaseConfig::CONFIG_FILE_BASE.'.pipelines', $config);
        if ($exception == '') {
            $this->assertEquals(null, SheetBaseConfig::checkAndReportError());
        } else {
            $this->assertIsString(SheetBaseConfig::checkAndReportError());
        }
    }
}
