<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointReadExcelTest extends ConfigTestCase
{
    protected static function content(string $separator): string
    {
        $data = [
            ['a', 'b'],
            [1, 2],
            [2, 3],
        ];
        $return = '';
        foreach ($data as $row) {
            $return .= implode($separator, $row).PHP_EOL;
        }

        return $return;
    }

    public static function dataProviderContent(): array
    {
        return [
            'psv' => ['/test.psv', self::content('|'), DummyEndpointReadPsv::class],
            'tsv' => ['/test.tsv', self::content("\t"), DummyEndpointReadTsv::class],
            'csv' => ['/test.csv', self::content(','), DummyEndpointReadCsv::class],
        ];
    }

    #[DataProvider('dataProviderContent')]
    /**
     * @throws EndpointCodeException
     */
    public function testReadExcels(string $path, string $content, string $readerClass)
    {
        $schema = new DummySheetBaseSchema;
        Storage::fake('sheet-base');
        Storage::disk('sheet-base')->put($path, $content);
        $pipelineData = new PipelineData($schema);
        $reader = new $readerClass;
        $reader->fillPipeline($pipelineData);

        $this->assertEquals([1 => ['b' => 2], 2 => ['b' => 3]], $pipelineData->toArray());
    }
}
