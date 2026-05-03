<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Traits\HasFileExtensions;
use Throwable;

/**
 * Class StorageFile
 *
 * Base class for endpoints that interact with files in the storage system.
 *
 * Main Responsibilities:
 * - Path Validation: Ensures the file path and extension are valid.
 * - Endpoint Interface: Implements basic endpoint functionality for file-based storage.
 *
 * Usage Example:
 * ```php
 * // Used as a base class for specific file endpoints
 * class MyFileEndpoint extends StorageFile { ... }
 * ```
 */
abstract class StorageFile extends StorageBase implements IsEndpoint
{
    use HasFileExtensions;

    /**
     * @throws EndpointCodeException
     * @throws FileSystemNotDefinedException
     * @throws Throwable
     */
    public function __construct(?string $path = null)
    {
        parent::__construct();
        $this->validatePathAndExtension($path);
    }

    public function toString(): string
    {
        return $this->path;
    }
}
