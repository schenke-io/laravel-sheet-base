<?php

namespace SchenkeIo\LaravelSheetBase\Helpers;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\MakeEndpointException;

class FindEndpointClass
{
    /** @var array<string, array<string, string>> */
    private static array $cache = [];

    /**
     * @return array<string, string>
     */
    public static function getWriters(): array
    {
        return self::discover('Writers', IsWriter::class);
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * @return array<string, string>
     */
    public static function getReaders(): array
    {
        return self::discover('Readers', IsReader::class);
    }

    /**
     * @return array<string, string>
     */
    private static function discover(string $subDir, string $interface): array
    {
        if (isset(self::$cache[$subDir])) {
            return self::$cache[$subDir];
        }
        $found = [];
        $files = File::glob(__DIR__.'/../Endpoints/'.$subDir.'/*.php');
        foreach ($files as $file) {
            $className = 'SchenkeIo\\LaravelSheetBase\\Endpoints\\'.$subDir.'\\'.basename($file, '.php');
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                if ($reflection->isInstantiable() && $reflection->implementsInterface($interface)) {
                    if ($reflection->hasProperty('extension')) {
                        $property = $reflection->getProperty('extension');
                        $instance = $reflection->newInstanceWithoutConstructor();
                        $extension = $property->getValue($instance);
                        if (is_string($extension) && $extension !== '') {
                            $found[$extension] = $className;
                        }
                    }
                }
            }
        }

        return self::$cache[$subDir] = $found;
    }

    /**
     * @throws MakeEndpointException
     */
    public static function fromSource(string $path): IsReader
    {
        $extension = self::getExtension($path);
        if ($class = self::getReaders()[$extension] ?? false) {
            /** @var IsReader $instance */
            $instance = new $class($path);

            return $instance;
        }
        throw new MakeEndpointException($path, "no reader found for '$extension'");
    }

    /**
     * @throws MakeEndpointException
     */
    public static function fromTarget(string $path): IsWriter
    {
        $extension = self::getExtension($path);
        if ($class = self::getWriters()[$extension] ?? false) {
            /** @var IsWriter $instance */
            $instance = new $class($path);

            return $instance;
        }
        throw new MakeEndpointException($path, "no writer found for '$extension'");
    }

    private static function getExtension(string $path): string
    {
        return pathinfo($path)['extension'] ?? '';
    }
}
