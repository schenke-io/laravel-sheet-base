<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Skills;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Skills\FindEndpointClass;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class MakeEndpointTest extends ConfigTestCase
{
    public static function dataProviderPathTest(): array
    {
        return [
            // name           isReader  path   isException   interface
            'empty read' => [true, '', true, IsReader::class],
            'psv read ok' => [true, 'psv/persons.psv', false, IsReader::class],
            'psv read unknown' => [true, 'psv/persons.abx', true, IsReader::class],
            'psv write ok' => [false, 'psv/something.neon', false, IsWriter::class],
            'psv write unknown' => [false, 'psv/something.abb', true, IsWriter::class],
        ];
    }

    /**
     * @dataProvider  dataProviderPathTest
     *
     * @throws FileSystemNotDefinedException
     * @throws EndpointCodeException
     * @throws \Throwable
     */
    public function testMakeEndpoint(bool $isReader, string $path, bool $isException, string $interface): void
    {
        if ($isException) {
            $this->expectException(MakeEndpointException::class);
        }
        if ($isReader) {
            $endpoint = FindEndpointClass::fromSource($path);
        } else {
            $endpoint = FindEndpointClass::fromTarget($path);
        }
        $this->assertInstanceOf($interface, $endpoint);
    }
}
