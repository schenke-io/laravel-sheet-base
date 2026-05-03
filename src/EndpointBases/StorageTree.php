<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use Throwable;

/**
 * Class StorageTree
 *
 * Base class for endpoints that manage a directory tree of files in storage.
 *
 * Main Responsibilities:
 * - Root Directory Management: Ensures a root directory is defined for the tree.
 * - File Base Validation: Tracks which file names (bases) are allowed within the tree.
 * - Endpoint Interface: Implements basic endpoint functionality for directory-based storage.
 *
 * Usage Example:
 * ```php
 * class MyStorageTree extends StorageTree {
 *     public string $root = 'translations';
 *     public array $fileBases = ['en', 'de', 'fr'];
 * }
 * ```
 */
abstract class StorageTree extends StorageBase implements IsEndpoint
{
    /**
     * root dir for the files to write
     */
    public string $root = '';

    /**
     * list of file-bases (made from keys) which are OK to be written
     *
     * @var array<int, string>
     */
    public array $fileBases = [];

    /**
     * @throws EndpointCodeException
     * @throws FileSystemNotDefinedException
     * @throws Throwable
     */
    public function __construct()
    {
        parent::__construct();
        $className = class_basename($this);
        if ($this->root === '') {
            throw new EndpointCodeException($className, "'public string \$root = ...' not set in ".get_class($this));
        }
        if (count($this->fileBases) == 0) {
            throw new EndpointCodeException($className, "'public array \$fileBases = [...]' not set in ".get_class($this));
        }
    }

    public function toString(): string
    {
        return $this->root.'/***';
    }
}
