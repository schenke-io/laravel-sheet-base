<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class MakeEndpointTest extends ConfigTestCase
{
    public static function dataProviderPathTest(): array
    {
        return [
            // name           isReader  path   isException   interface
            'empty read' => [true, '', true, IsReader::class],
            'read unknown' => [true, 'neon/persons.abx', true, IsReader::class],

            'neon read ok' => [true, 'neon/persons.neon', false, IsReader::class],
            'psv read ok' => [true, 'psv/persons.psv', false, IsReader::class],
            'csv read ok' => [true, 'csv/persons.csv', false, IsReader::class],
            'tsv read ok' => [true, 'psv/persons.psv', false, IsReader::class],
            'txt read ok' => [true, 'txt/persons.txt', false, IsReader::class],
            'yaml read ok' => [true, 'yaml/persons.yaml', false, IsReader::class],
            'yml read ok' => [true, 'yaml/persons.yml', false, IsReader::class],

            'write unknown' => [false, 'psv/something.abb', true, IsWriter::class],

            'psv write ok' => [false, 'psv/something.neon', false, IsWriter::class],
            'csv write ok' => [false, 'csv/something.csv', false, IsWriter::class],
            'tsv write ok' => [false, 'tsv/something.tsv', false, IsWriter::class],
            'neon write ok' => [false, 'neon/something.neon', false, IsWriter::class],
            'php write ok' => [false, 'php/something.php', false, IsWriter::class],
            'json write ok' => [false, 'json/something.json', false, IsWriter::class],
            'txt write ok' => [false, 'txt/something.txt', false, IsWriter::class],
            'yaml write ok' => [false, 'yaml/something.yaml', false, IsWriter::class],
            'yml write ok' => [false, 'yml/something.yml', false, IsWriter::class],
        ];
    }

    #[DataProvider('dataProviderPathTest')]
    /**
     * @throws FileSystemNotDefinedException
     * @throws EndpointCodeException
     * @throws \Throwable
     */
    public function test_make_endpoint(bool $isReader, string $path, bool $isException, string $interface): void
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
