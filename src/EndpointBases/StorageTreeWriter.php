<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

/**
 * Class StorageTreeWriter
 *
 * Abstract base class for writing data into a directory tree in storage.
 *
 * Main Responsibilities:
 * - Scoped Writing: Ensures all files are written within the specified root directory.
 * - Storage Interaction: Wraps Laravel Storage facade to write content to the disk.
 * - Writer Interface: Implements IsWriter for integration with the pipeline release process.
 *
 * Usage Example:
 * ```php
 * class MyTreeWriter extends StorageTreeWriter {
 *     public function releasePipeline(PipelineData $pipelineData, string $writingClass): void { ... }
 * }
 * ```
 */
abstract class StorageTreeWriter extends StorageTree implements IsWriter
{
    /**
     * @throws EndpointCodeException
     */
    protected function storagePut(string $path, string $content): void
    {
        /*
         * $path must be under the root
         */
        if (! str_starts_with($path, $this->root)) {
            throw new EndpointCodeException(
                class_basename($this),
                sprintf(
                    'the given file %s is not under the given root %s',
                    $path, $this->root
                )
            );
        }
        Storage::disk($this->disk)->put($path, $content);
    }

    public function explain(): string
    {
        return 'writes under this root '.$this->root;
    }
}
