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
     * @throws \Throwable
     */
    public function __construct()
    {
        throw_unless(
            is_array(config('filesystems.disks.'.$this->disk)),
            new FileSystemNotDefinedException(
                "the file system disk '{$this->disk}' is not defined in /config/filesystems.php"
            )
        );
    }
}
