<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Endpoints\EndpointReadPsv;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointReadPsvTest extends ConfigTestCase
{
    /**
     * @throws ReadParseException
     */
    public function testReadPsv()
    {
        $path = '/test.psv';
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
                $this->addId('a');
                $this->addUnsigned('b');
            }
        };
        Storage::fake('sheet-base');
        $content = <<<'NEON'

a|b
1|2
2|3
        
NEON;

        Storage::disk('sheet-base')->put($path, $content);
        $psv = new class extends EndpointReadPsv
        {
            public string $path = '/test.psv';
        };
        $pipelineData = new PipelineData($schema);
        $psv->fillPipeline($pipelineData);

        $this->assertEquals([1 => ['b' => 2], 2 => ['b' => 3]], $pipelineData->toArray());
    }
}
