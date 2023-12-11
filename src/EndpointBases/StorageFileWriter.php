<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;

abstract class StorageFileWriter extends StorageFile implements IsWriter
{
    protected function storagePut(string $path, string $content): void
    {
        Storage::disk(self::DISK)->put($path, $content);
    }

    public function explain(): string
    {
        return 'writes to '.$this->path;
    }
}
