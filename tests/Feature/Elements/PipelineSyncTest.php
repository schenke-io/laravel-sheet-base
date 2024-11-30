<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use Google\Service\Exception as GoogleServiceException;
use Illuminate\Console\Command;
use Mockery;
use SchenkeIo\LaravelSheetBase\Elements\Pipeline;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\DummyGoogleSheetRead;
use Workbench\App\Endpoints\LangFilter;
use Workbench\App\Endpoints\LangSchema;
use Workbench\App\Endpoints\LangWrite;

class PipelineSyncTest extends ConfigTestCase
{
    /**
     * @throws GoogleServiceException
     * @throws EndpointCodeException
     * @throws ConfigErrorException
     * @throws SchemaVerifyColumnsException
     * @throws MakeEndpointException
     */
    public function test_it_can_sync(): void
    {
        $sheet = Mockery::mock(DummyGoogleSheetRead::class);
        $sheet->shouldReceive('fillPipeline')->once();
        $sheet->shouldReceive('explain')->once();

        $config = [
            'sources' => [$sheet],
            'target' => LangWrite::class,
            'schema' => LangSchema::class,
            'filter' => LangFilter::class,
            'sync' => true,
        ];
        $cmd = Mockery::mock(Command::class);
        $cmd->shouldReceive('info')->times(4);
        $pipeline = Pipeline::fromConfig($config, '');
        $pipeline->pump($cmd, '', '');
    }

    /**
     * @throws GoogleServiceException
     * @throws EndpointCodeException
     * @throws SchemaVerifyColumnsException
     * @throws MakeEndpointException
     */
    public function test_defect_keys_in_config(): void
    {
        $config = [
            'sources' => [DummyGoogleSheetRead::class],
            'target' => LangWrite::class,
            'schemax' => LangSchema::class,
            'filtr' => LangFilter::class,
            'sync' => true,
        ];
        $cmd = Mockery::mock(Command::class);
        $this->expectException(ConfigErrorException::class);
        $pipeline = Pipeline::fromConfig($config, '');
        $pipeline->pump($cmd, '', '');
    }
}
