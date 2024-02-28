<?php

namespace SchenkeIo\LaravelSheetBase\Skills;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadNeon;
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadPsv;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteJson;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteNeon;
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWritePhp;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

class MakeEndpoint
{
    /**
     * @throws FileSystemNotDefinedException
     * @throws ReadParseException
     * @throws MakeEndpointException|\Throwable
     */
    public static function fromSource(string $path): IsReader
    {
        $extension = self::getExtension($path);

        return match ($extension) {
            'psv' => new EndpointReadPsv($path),
            'neon' => new EndpointReadNeon($path),
            default => throw new MakeEndpointException($path, "no reader found for '$extension'")
        };
    }

    /**
     * @throws ReadParseException
     * @throws FileSystemNotDefinedException
     * @throws MakeEndpointException|\Throwable
     */
    public static function fromTarget(string $path): IsWriter
    {
        $extension = self::getExtension($path);

        return match ($extension) {
            'php' => new EndpointWritePhp($path),
            'neon' => new EndpointWriteNeon($path),
            'json' => new EndpointWriteJson($path),
            default => throw new MakeEndpointException($path, "no writer found for '$extension'")
        };
    }

    private static function getExtension(string $path): string
    {
        return pathinfo($path)['extension'] ?? '';
    }
}
