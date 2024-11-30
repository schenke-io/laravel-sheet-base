<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

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
