<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\EndpointReadNeon;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointReadNeonTest extends ConfigTestCase
{
    public static function dataProviderContent(): array
    {
        return [
            'ok' => [[1 => ['b' => 2], 2 => ['b' => 3]], '[{a: 1,b: 2},{a: 2,b: 3}]'],
            'neon syntax error' => [ReadParseException::class, '[{a: 1,b: 2},{a: 2,:3]'],
            'neon no array' => [ReadParseException::class, 'a 45'],
        ];
    }

    /**
     * @dataProvider dataProviderContent
     *
     * @throws ReadParseException
     */
    public function testReadFile(mixed $expectation, string $content)
    {
        $path = '/test.neon';
        if (! is_array($expectation)) {
            $this->expectException($expectation);
        }
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addId('a');
                $this->addUnsigned('b');
            }
        };

        Storage::fake('sheet-base');
        Storage::disk('sheet-base')->put($path, $content);
        $pipelineData = new PipelineData($schema);
        $neon = new class extends EndpointReadNeon
        {
            public string $path = '/test.neon';
        };
        $neon->fillPipeline($pipelineData);
        if (is_array($expectation)) {
            $this->assertEquals($expectation, $pipelineData->toArray());
        }

    }
}
