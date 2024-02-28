<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;

abstract class StorageBase implements IsEndpoint
{
    public const DISK = 'sheet-base';

    /**
     * @throws FileSystemNotDefinedException
     * @throws \Throwable
     */
    public function __construct()
    {
        throw_unless(
            is_array(config('filesystems.disks.sheet-base')),
            new FileSystemNotDefinedException("the file system disk 'sheet-base' is not defined in /config/filesystems.php")
        );
    }
}
