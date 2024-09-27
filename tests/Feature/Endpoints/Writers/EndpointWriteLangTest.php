<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Writers;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageBase;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\LangSchema;

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
            'root defect' => [EndpointCodeException::class, '/lang_error', ['home'], $dataGood],
            'data defect' => [EndpointCodeException::class, '/lang', ['home'], $dataBad],
            'bases defect' => [EndpointCodeException::class, '/lang', [], $dataGood],
        ];
    }

    /**
     * @dataProvider dataProviderRows
     *
     * @throws EndpointCodeException
     */
    public function testWriteLang(string $exception, string $root, array $fileBases, array $rows): void
    {
        Storage::fake(StorageBase::DEFAULT_DISK);
        if ($exception == '') {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException($exception);
        }
        $fileBasesPhp = var_export($fileBases, true);
        $writeLang = null;
        $php = <<<PHP
\$writeLang = new class extends \SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteLang {
            public string \$root = '$root';
            public array \$fileBases = $fileBasesPhp;
        };

PHP;
        //        dump($php);
        eval($php); // make the class
        $pipeLineData = new PipelineData(new LangSchema);
        foreach ($rows as $row) {
            $pipeLineData->addRow($row);
        }
        $writeLang->releasePipeline($pipeLineData, '');
    }
}
