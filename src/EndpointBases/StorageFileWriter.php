<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Traits\HasFileExtensions;
use Throwable;

/**
 * Class StorageFileWriter
 *
 * Abstract base class for writing data to files in the file system.
 *
 * Main Responsibilities:
 * - Path Management: Handles the destination file path and provides human-readable explanations.
 * - Writer Interface: Implements IsWriter for integration with the pipeline release process.
 *
 * Usage Example:
 * ```php
 * class MyFileWriter extends StorageFileWriter {
 *     public function releasePipeline(PipelineData $pipelineData, string $writingClass): void { ... }
 * }
 * ```
 */
abstract class StorageFileWriter extends StorageBase implements IsWriter
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

    /**
     * Explain the writing action.
     */
    public function explain(): string
    {
        return 'writes to '.$this->path;
    }
}
