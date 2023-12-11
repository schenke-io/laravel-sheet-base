<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Skills;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageBase;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use SchenkeIo\LaravelSheetBase\Skills\MakeEndpoint;
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
     * @throws ReadParseException
     */
    public function testMakeEndpoint(bool $isReader, string $path, bool $isException, string $interface): void
    {
        //     Storage::fake(StorageBase::DISK);
        if ($isException) {
            $this->expectException(MakeEndpointException::class);
            //        }else{
            //            $this->expectNotToPerformAssertions();
        }
        if ($isReader) {
            $endpoint = MakeEndpoint::fromSource($path);
        } else {
            $endpoint = MakeEndpoint::fromTarget($path);
        }
        $this->assertInstanceOf($interface, $endpoint);
    }
}
