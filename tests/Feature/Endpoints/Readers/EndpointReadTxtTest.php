<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\PipelineType;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadTxt;
use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointReadTxtTest extends ConfigTestCase
{
    public static function dataProviderContent(): array
    {
        return [
            'ok 1' => [
                [
                    1 => null,
                    2 => null,
                ], "1\n2\n"],
            'ok 2' => [
                [
                    1 => null,
                    3 => null,
                ], "1\n\n\n3\n"],
            'ok 3' => [
                [
                    3 => null,
                ], "\n3\n"],
        ];
    }

    #[DataProvider('dataProviderContent')]
    /**
     * @throws DataReadException
     */
    public function test_read_file(mixed $expectation, string $content)
    {
        $path = '/test.txt';
        if (! is_array($expectation)) {
            $this->expectException($expectation);
        }
        $schema = new DummyOnlyIdSheetBaseSchema;

        Storage::fake('sheet-base');
        Storage::disk('sheet-base')->put($path, $content);
        $pipelineData = new PipelineData($schema);
        $yaml = new DummyEndpointReadTxt;
        $yaml->fillPipeline($pipelineData);
        if (is_array($expectation)) {
            $this->assertEquals($expectation, $pipelineData->toArray());
        }

    }

    public function test_read_file_fails()
    {
        Storage::fake('sheet-base');
        Storage::disk('sheet-base')->put('txt/persons.txt', '123');
        Storage::shouldReceive('disk')->twice()->andReturnSelf();
        Storage::shouldReceive('exists')->once()->andReturn(true);
        Storage::shouldReceive('get')->andReturn(null);

        $this->expectException(DataReadException::class);

        $endpoint = new EndpointReadTxt('txt/persons.txt');
        $pipelineData = PipelineData::fromType(PipelineType::Table);
        $endpoint->fillPipeline($pipelineData);
    }
}
