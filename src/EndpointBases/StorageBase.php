<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;

abstract class StorageBase implements IsEndpoint
{
    public const DISK = 'sheet-base';

    /**
     * @throws FileSystemNotDefinedException
     */
    public function __construct()
    {
        if (! is_array(config('filesystems.disks.sheet-base'))) {
            throw new FileSystemNotDefinedException("the file system disk 'sheet-base' is not defined in /config/filesystems.php");
        }
    }
}
