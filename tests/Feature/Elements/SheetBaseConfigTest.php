<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\LangSchema;
use Workbench\App\Endpoints\LangWrite;
use Workbench\App\Endpoints\LangWrite2;
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
            'multiple use of same target' => [ConfigErrorException::class,
                [
                    'persons' => [
                        'sources' => ['psv/persons.psv'],
                        'schema' => PersonSchema::class,
                        'target' => PersonsWriteNeon::class,
                    ],
                    'persons2' => [
                        'sources' => [PersonsReadPsv::class],
                        'schema' => PersonSchema::class,
                        'target' => PersonsWriteNeon::class,
                    ],
                ],
            ],
            'multiple use of language' => [ConfigErrorException::class,
                [
                    'persons' => [
                        'sources' => [PersonsReadPsv::class],
                        'schema' => LangSchema::class,
                        'target' => LangWrite::class,
                    ],
                    'persons2' => [
                        'sources' => [PersonsReadPsv::class],
                        'schema' => LangSchema::class,
                        'target' => LangWrite2::class,
                    ],
                ],
            ],

        ];
    }

    /**
     * @throws \Throwable
     * @throws EndpointCodeException
     * @throws FileSystemNotDefinedException
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     * @throws MakeEndpointException
     */
    #[DataProvider('dataProvidedConfig')]
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

    #[DataProvider('dataProvidedConfig')]
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
