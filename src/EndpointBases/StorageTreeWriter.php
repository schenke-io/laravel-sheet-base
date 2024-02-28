<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
use Throwable;

abstract class StorageTreeWriter extends StorageTree implements IsWriter
{
    /**
     * @throws ReadParseException
     * @throws Throwable
     */
    protected function storagePut(string $path, string $content): void
    {
        /*
         * $path must be under the root
         */
        throw_unless(
            str_starts_with($path, $this->root),
            new ReadParseException(
                class_basename($this),
                sprintf(
                    'the given file %s is not under the given root %s',
                    $path, $this->root
                )
            )
        );
        Storage::disk(self::DISK)->put($path, $content);
    }

    public function explain(): string
    {
        return 'writes under this root '.$this->root;
    }
}
