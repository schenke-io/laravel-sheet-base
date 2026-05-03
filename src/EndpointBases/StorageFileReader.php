<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Traits\HasFileExtensions;
use Throwable;

/**
 * Class StorageFileReader
 *
 * Abstract base class for reading data from files stored in the file system.
 *
 * Main Responsibilities:
 * - existence Check: Verifies that the file exists in the specified storage before reading.
 * - Path Management: Handles the file path and provides human-readable explanations.
 * - Reader Interface: Implements IsReader for integration with the pipeline.
 *
 * Usage Example:
 * ```php
 * class MyFileReader extends StorageFileReader {
 *     public function fillPipeline(PipelineData &$pipelineData): void { ... }
 * }
 * ```
 */
abstract class StorageFileReader extends StorageBase implements IsReader
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

        if (! $this->storageExists($this->path)) {
            throw new EndpointCodeException(
                class_basename($this),
                sprintf(
                    'class %s was unable to find file %s in disk %s',
                    get_class($this),
                    $this->getStorageRoot().$this->path,
                    $this->disk
                )
            );
        }
    }

    public function toString(): string
    {
        return $this->path;
    }

    public function explain(): string
    {
        return 'reads from '.$this->path;
    }
}
