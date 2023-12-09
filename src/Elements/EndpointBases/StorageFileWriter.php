<?php

namespace SchenkeIo\LaravelSheetBase\Elements\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;

abstract class StorageFileWriter extends StorageFile implements IsWriter
{
    protected function storagePut(string $path, string $content): void
    {
        Storage::disk(self::DISK)->put($path, $content);
    }
}
