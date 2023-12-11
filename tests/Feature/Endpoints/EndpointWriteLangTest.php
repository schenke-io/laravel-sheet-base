<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageBase;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Tests\data\LangSchema;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointWriteLangTest extends ConfigTestCase
{
    public static function dataProviderRows(): array
    {
        $dataGood = [
            ['id' => 'home.title', 'de' => 'Startseite', 'en' => 'Homepage'],
            ['id' => 'home.description', 'de' => 'Startseite', 'en' => 'Homepage'],
            ['id' => 'home.keywords', 'de' => 'Startseite', 'en' => 'Homepage'],
        ];
        $dataBad = [
            ['id' => 'home', 'de' => 'Startseite', 'en' => 'Homepage'],
        ];

        return [
            'all fine' => ['', '/lang', ['home'], $dataGood],
            'root defect' => [ReadParseException::class, '/lang_error', ['home'], $dataGood],
            'data defect' => [ReadParseException::class, '/lang', ['home'], $dataBad],
            'bases defect' => [ReadParseException::class, '/lang', [], $dataGood],
        ];
    }

    /**
     * @dataProvider dataProviderRows
     *
     * @throws ReadParseException
     */
    public function testWriteLang(string $exception, string $root, array $fileBases, array $rows): void
    {
        Storage::fake(StorageBase::DISK);
        if ($exception == '') {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException($exception);
        }
        $fileBasesPhp = var_export($fileBases, true);
        $writeLang = null;
        $php = <<<PHP
\$writeLang = new class extends SchenkeIo\LaravelSheetBase\Endpoints\EndpointWriteLang {
            public string \$root = '$root';
            public array \$fileBases = $fileBasesPhp;
        };

PHP;
        //        dump($php);
        eval($php); // make the class
        $pipeLineData = new PipelineData(new LangSchema());
        foreach ($rows as $row) {
            $pipeLineData->addRow($row);
        }
        $writeLang->releasePipeline($pipeLineData, '');
    }
}
