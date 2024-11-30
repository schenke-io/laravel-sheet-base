<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointReadYamlTest extends ConfigTestCase
{
    public static function dataProviderContent(): array
    {
        return [
            'ok' => [
                [
                    1 => ['b' => 2],
                    2 => ['b' => 3],
                ], '[{a: 1,b: 2},{a: 2,b: 3}]'],
            'yaml syntax error' => [EndpointCodeException::class, '[{a: 1,b: 2},{a: 2,:3]'],
            'yaml no array' => [EndpointCodeException::class, '1'],
        ];
    }

    #[DataProvider('dataProviderContent')]
    /**
     * @throws EndpointCodeException|DataReadException
     */
    public function test_read_file(mixed $expectation, string $content)
    {
        $path = '/test.yaml';
        if (! is_array($expectation)) {
            $this->expectException($expectation);
        }
        $schema = new DummySheetBaseSchema;

        Storage::fake('sheet-base');
        Storage::disk('sheet-base')->put($path, $content);
        $pipelineData = new PipelineData($schema);
        $yaml = new DummyEndpointReadYaml;
        $yaml->fillPipeline($pipelineData);
        if (is_array($expectation)) {
            $this->assertEquals($expectation, $pipelineData->toArray());
        }

    }
}
