<?php

namespace SchenkeIo\LaravelSheetBase\Helpers;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadCsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadNeon;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadPsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadTsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadTxt;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadYaml;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadYml;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteCsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteJson;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteNeon;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWritePhp;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWritePsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteTsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteTxt;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteYaml;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteYml;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;

class FindEndpointClass
{
    /**
     * @throws FileSystemNotDefinedException
     * @throws EndpointCodeException
     * @throws MakeEndpointException
     */
    public const WRITERS = [
        'php' => EndpointWritePhp::class,
        'neon' => EndpointWriteNeon::class,
        'json' => EndpointWriteJson::class,
        'psv' => EndpointWritePsv::class,
        'tsv' => EndpointWriteTsv::class,
        'txt' => EndpointWriteTxt::class,
        'csv' => EndpointWriteCsv::class,
        'yaml' => EndpointWriteYaml::class,
        'yml' => EndpointWriteYml::class,
    ];

    public const READERS = [
        'csv' => EndpointReadCsv::class,
        'psv' => EndpointReadPsv::class,
        'tsv' => EndpointReadTsv::class,
        'txt' => EndpointReadTxt::class,
        'neon' => EndpointReadNeon::class,
        'yaml' => EndpointReadYaml::class,
        'yml' => EndpointReadYml::class,
    ];

    /**
     * @throws MakeEndpointException
     */
    public static function fromSource(string $path): IsReader
    {
        $extension = self::getExtension($path);
        if ($class = self::READERS[$extension] ?? false) {
            return new $class($path);
        }
        throw new MakeEndpointException($path, "no reader found for '$extension'");
    }

    /**
     * @throws MakeEndpointException
     */
    public static function fromTarget(string $path): IsWriter
    {
        $extension = self::getExtension($path);
        if ($class = self::WRITERS[$extension] ?? false) {
            return new $class($path);
        }
        throw new MakeEndpointException($path, "no writer found for '$extension'");
    }

    private static function getExtension(string $path): string
    {
        return pathinfo($path)['extension'] ?? '';
    }
}
