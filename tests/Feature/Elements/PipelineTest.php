<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use SchenkeIo\LaravelSheetBase\Elements\Pipeline;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummyRead;
use Workbench\App\Endpoints\DummySchema;
use Workbench\App\Endpoints\DummyWrite;
use Workbench\App\Endpoints\PersonSchema;
use Workbench\App\Endpoints\PersonsReadPsv;
use Workbench\App\Endpoints\PersonsWriteNeon;

class PipelineTest extends ConfigTestCase
{
    protected const config = [
        'sources' => [DummyRead::class],
        'target' => DummyWrite::class,
        'schema' => DummySchema::class,
    ];

    public static function dataProviderConfig(): array
    {
        return [
            'correct' => ['', self::config],
            'sources not as array' => [ConfigErrorException::class,
                [
                    'sources' => PersonsReadPsv::class,
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'wrong target class' => [ConfigErrorException::class,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => PersonSchema::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'target class do not exists' => [ConfigErrorException::class,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => 'do not exists',
                    'schema' => PersonSchema::class,
                ],
            ],
            'wrong schema class' => [ConfigErrorException::class,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonsWriteNeon::class,
                ],
            ],
            'schema class not exists' => [ConfigErrorException::class,
                [
                    'sources' => [PersonsReadPsv::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => 'not exists',
                ],
            ],
            'empty source' => [ConfigErrorException::class,
                [
                    'sources' => [],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'defect source' => [ConfigErrorException::class,
                [
                    'sources' => ['unknown class'],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
            'wrong source' => [ConfigErrorException::class,
                [
                    'sources' => [PersonSchema::class],
                    'target' => PersonsWriteNeon::class,
                    'schema' => PersonSchema::class,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderConfig
     *
     * @throws ConfigErrorException
     */
    public function testCanMakeFromConfig(string $exception, array $config): void
    {
        if ($exception == '') {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException($exception);
        }
        Pipeline::fromConfig($config, '');

    }

    /**
     * @throws ConfigErrorException
     */
    public function testPump(): void
    {

        $pipeline = Pipeline::fromConfig(self::config, '');
        $counter = 0;
        $dummyTestFunction = function () use (&$counter) {
            $counter++;
        };
        $pipeline->pump($dummyTestFunction, '', '');
        $this->assertGreaterThan(2, $counter, 'callback to be called moe than 2 times');
    }
}
