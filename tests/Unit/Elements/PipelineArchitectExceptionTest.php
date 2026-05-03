<?php

use SchenkeIo\LaravelSheetBase\Elements\PipelineArchitect;
use SchenkeIo\LaravelSheetBase\Exceptions\ArchitectureException;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;
use SchenkeIo\LaravelSheetBase\Tests\TestCase;

uses(TestCase::class);

test('PipelineArchitect throws exception when naming convention is violated', function () {
    $invalidFilePath = __DIR__.'/../../../src/Endpoints/Readers/InvalidReader.php';
    $content = <<<'PHP'
<?php
namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageFileReader;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
class InvalidReader extends StorageFileReader {
    public string $extension = 'invalid';
    public function fillPipeline(PipelineData &$pipelineData): void {}
}
PHP;
    file_put_contents($invalidFilePath, $content);

    // Clear cache
    FindEndpointClass::clearCache();

    try {
        $architect = new PipelineArchitect;
        $architect->scan();
    } catch (ArchitectureException $e) {
        expect($e->getMessage())->toContain('Read-file not found for extension Invalid');

        // Cleanup after catch
        if (file_exists($invalidFilePath)) {
            unlink($invalidFilePath);
        }

        // Clear cache again to not affect other tests
        FindEndpointClass::clearCache();

        return;
    }

    // Cleanup if no exception thrown (fail case)
    if (file_exists($invalidFilePath)) {
        unlink($invalidFilePath);
    }
    FindEndpointClass::clearCache();

    $this->fail('ArchitectureException was not thrown');
});
