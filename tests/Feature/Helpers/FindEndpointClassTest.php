<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadCsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\NotAnEndpoint;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteCsv;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;

beforeEach(function () {
    config()->set('filesystems.disks.sheet-base', [
        'driver' => 'local',
        'root' => __DIR__,
    ]);
    if (! file_exists(__DIR__.'/test.csv')) {
        touch(__DIR__.'/test.csv');
    }
    FindEndpointClass::clearCache();
});

afterAll(function () {
    if (file_exists(__DIR__.'/test.csv')) {
        unlink(__DIR__.'/test.csv');
    }
});

test('get writers', function () {
    $writers = FindEndpointClass::getWriters();
    expect($writers)->toBeArray()
        ->and($writers)->toHaveKey('csv')
        ->and($writers['csv'])->toBe(EndpointWriteCsv::class);
});

test('get readers', function () {
    $readers = FindEndpointClass::getReaders();
    expect($readers)->toBeArray()
        ->and($readers)->toHaveKey('csv')
        ->and($readers['csv'])->toBe(EndpointReadCsv::class);
});

test('from source ok', function () {
    $instance = FindEndpointClass::fromSource('test.csv');
    expect($instance)->toBeInstanceOf(EndpointReadCsv::class);
});

test('from source fail', function () {
    FindEndpointClass::fromSource('test.unknown');
})->throws(MakeEndpointException::class);

test('from target ok', function () {
    $instance = FindEndpointClass::fromTarget('test.csv');
    expect($instance)->toBeInstanceOf(EndpointWriteCsv::class);
});

test('from target fail', function () {
    FindEndpointClass::fromTarget('test.unknown');
})->throws(MakeEndpointException::class);

test('get extension empty', function () {
    FindEndpointClass::fromSource('test');
})->throws(MakeEndpointException::class);

test('uncovered branches', function () {
    File::shouldReceive('glob')
        ->once()
        ->andReturn([
            'NonExistent.php',  // class_exists will be false
            'NotAnEndpoint.php', // implementsInterface will be false
            'NoExtension.php',    // property_exists will be false
            'EmptyExtension.php',  // extension will be empty
        ]);

    // Define dummy classes in the expected namespace
    if (! class_exists(NotAnEndpoint::class)) {
        eval('namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers; 
              class NotAnEndpoint {} 
              class NoExtension implements \SchenkeIo\LaravelSheetBase\Contracts\IsReader {
                  public function explain(): string { return ""; }
                  public function toString(): string { return ""; }
                  public function fillPipeline(\SchenkeIo\LaravelSheetBase\Elements\PipelineData &$pipelineData): void {}
              }
              class EmptyExtension implements \SchenkeIo\LaravelSheetBase\Contracts\IsReader {
                  public string $extension = "";
                  public function explain(): string { return ""; }
                  public function toString(): string { return ""; }
                  public function fillPipeline(\SchenkeIo\LaravelSheetBase\Elements\PipelineData &$pipelineData): void {}
              }
              ');
    }

    $readers = FindEndpointClass::getReaders();
    expect($readers)->toBeArray();

    // call again to cover cache
    expect(FindEndpointClass::getReaders())->toBe($readers);
});

test('empty glob', function () {
    File::shouldReceive('glob')
        ->once()
        ->andReturn([]);

    $readers = FindEndpointClass::getReaders();
    expect($readers)->toBeEmpty();
});
