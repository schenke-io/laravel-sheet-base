<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use Throwable;

abstract class StorageFileReader extends StorageFile implements IsReader
{
    /**
     * @throws EndpointCodeException
     * @throws FileSystemNotDefinedException
     * @throws Throwable
     */
    public function __construct(?string $path = null)
    {
        parent::__construct($path);
        throw_unless($this->storageExists($this->path),
            new EndpointCodeException(
                class_basename($this),
                sprintf(
                    'class %s was unable to find file %s in disk %s',
                    get_class($this),
                    $this->getStorageRoot().$this->path,
                    self::DISK,

                )
            )
        );

    }

    protected function storageGet(string $path): string
    {
        return Storage::disk(self::DISK)->get($path);
    }

    public function explain(): string
    {
        return 'reads from '.$this->path;
    }
}
