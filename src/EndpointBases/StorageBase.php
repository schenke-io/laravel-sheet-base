<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Traits\HasStorage;

/**
 * Class StorageBase
 *
 * Base class for all endpoints interacting with Laravel's storage disks.
 *
 * Main Responsibilities:
 * - Disk Management: Ensures the storage disk is properly configured and accessible.
 * - String Representation: Provides a consistent string format for storage-based endpoints.
 */
abstract class StorageBase implements IsEndpoint
{
    use HasStorage;

    /**
     * @throws FileSystemNotDefinedException
     */
    public function __construct()
    {
        $this->checkDisk();
    }

    public function toString(): string
    {
        return 'storage:'.$this->disk;
    }
}
