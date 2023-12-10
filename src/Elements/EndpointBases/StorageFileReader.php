<?php

namespace SchenkeIo\LaravelSheetBase\Elements\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

abstract class StorageFileReader extends StorageFile implements IsReader
{
    /**
     * @throws ReadParseException
     * @throws FileSystemNotDefinedException
     */
    public function __construct()
    {
        parent::__construct();
        if (! $this->storageExists($this->path)) {
            throw new ReadParseException(
                sprintf(
                    'class %s was unable to find file %s in disk %s',
                    get_class($this),
                    $this->getStorageRoot().$this->path,
                    self::DISK,

                )
            );
        }
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
