<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;

abstract class StorageBase implements IsEndpoint
{
    public const DEFAULT_DISK = 'sheet-base';

    public string $disk = self::DEFAULT_DISK;

    /**
     * @throws FileSystemNotDefinedException
     */
    public function __construct()
    {
        if (! is_array(config('filesystems.disks.'.$this->disk))) {
            throw new FileSystemNotDefinedException(
                "the file system disk '{$this->disk}' is not defined in /config/filesystems.php"
            );
        }
    }

    public function toString(): string
    {
        return 'storage:'.$this->disk;
    }
}
