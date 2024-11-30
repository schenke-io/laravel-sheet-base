<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelSheetBase\Elements\Pipeline;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummyFilter;
use Workbench\App\Endpoints\DummyGoogleSheetRead;
use Workbench\App\Endpoints\DummyRead;
use Workbench\App\Endpoints\DummySchema;
use Workbench\App\Endpoints\DummyWrite;
use Workbench\App\Endpoints\LangRead;
use Workbench\App\Endpoints\LangSchema;
use Workbench\App\Endpoints\LangWrite;
use Workbench\App\Endpoints\PersonSchema;
use Workbench\App\Endpoints\PersonsReadPsv;
use Workbench\App\Endpoints\PersonsWriteNeon;

class PipelineTest extends ConfigTestCase
{
    protected const correct = [
        'sources' => [DummyRead::class],
        'target' => DummyWrite::class,
        'schema' => DummySchema::class,
    ];

    public static function dataProviderConfig(): array
    {
        return [
            'correct' => ['', 0, self::correct],
            'from files' => ['', 0,
                [
                    'sources' => ['psv/persons.psv'],
                    'target' => 'txt/_temp.txt',
                    'schema' => PersonSchema::class,
                ],
            ],
            'sources not as array' => [ConfigErrorException::class, 4,
                [
                    'sources' => PersonsReadPsv::class,
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'wrong target class' => [ConfigErrorException::class, 8,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => PersonSchema::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'no class name and invalid target file' => [MakeEndpointException::class, -1,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => 'do not exists',
                    'schema' => PersonSchema::class,
                ],
            ],
            'target is just a file name' => ['', 0,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => 'something.neon',
                    'schema' => PersonSchema::class,
                ],
            ],
            'wrong schema class' => [ConfigErrorException::class, 3,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonsWriteNeon::class,
                ],
            ],
            'schema class not exists' => [ConfigErrorException::class, 2,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => 'not exists',
                ],
            ],
            'empty source' => [ConfigErrorException::class, 5,
                [
                    'sources' => [],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'no class name and invalid source file' => [MakeEndpointException::class, 0,
                [
                    'sources' => ['unknown class'],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'wrong source' => [ConfigErrorException::class, 6,
                [
                    'sources' => [PersonsWriteNeon::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'empty target' => [ConfigErrorException::class, 7,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => '',
                    'schema' => PersonSchema::class,
                ],
            ],
            'filter is writer class' => [ConfigErrorException::class, 1,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                    'filter' => PersonsWriteNeon::class,
                ],
            ],
            'sync impossible with two sources' => [ConfigErrorException::class, 12,
                [
                    'sources' => [DummyGoogleSheetRead::class, PersonsReadPsv::class],
                    'target' => LangWrite::class,
                    'schema' => LangSchema::class,
                    'filter' => DummyFilter::class,
                    'sync' => true,
                ],
            ],
            'sync requires Google sheet source' => [ConfigErrorException::class, 11,
                [
                    'sources' => [LangRead::class],
                    'target' => LangWrite::class,
                    'schema' => LangSchema::class,
                    'filter' => DummyFilter::class,
                    'sync' => true,
                ],
            ],
            'sync impossible with non language target' => [ConfigErrorException::class, 13,
                [
                    'sources' => [DummyGoogleSheetRead::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => LangSchema::class,
                    'filter' => DummyFilter::class,
                    'sync' => true,
                ],
            ],
            'sync impossible without filter' => [ConfigErrorException::class, 14,
                [
                    'sources' => [DummyGoogleSheetRead::class],
                    'target' => LangWrite::class,
                    'schema' => LangSchema::class,
                    'sync' => true,
                ],
            ],
            'working filter' => ['', 0,
                [
                    'sources' => [DummyRead::class],
                    'target' => DummyWrite::class,
                    'schema' => DummySchema::class,
                    'filter' => DummyFilter::class,
                ],
            ],
            'working filter file' => ['', 0,
                [
                    'sources' => [DummyRead::class],
                    'target' => DummyWrite::class,
                    'schema' => DummySchema::class,
                    'filter' => 'psv/persons.psv',
                ],
            ],
        ];
    }

    /**
     * @throws \Throwable
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     * @throws MakeEndpointException
     */
    #[DataProvider('dataProviderConfig')]
    public function test_can_make_from_config(string $exception, int $code, array $config): void
    {
        if ($exception == '') {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException($exception);
            if ($exception === ConfigErrorException::class) {
                $this->expectExceptionMessageMatches("/ $code\$/");
            }

        }
        Pipeline::fromConfig($config, '');

    }

    /**
     * @throws \Throwable
     * @throws EndpointCodeException
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     * @throws MakeEndpointException
     */
    public function test_pump(): void
    {
        $cmd = Mockery::mock(Command::class);
        $cmd->shouldReceive('info')->times(3);
        $pipeline = Pipeline::fromConfig(self::correct, '');
        $pipeline->pump($cmd, '', '');
    }
}
