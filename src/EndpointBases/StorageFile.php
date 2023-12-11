<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

abstract class StorageFile extends StorageBase implements IsEndpoint
{
    /**
     * path in filesystems, needs to be overwritten
     */
    public string $path = '';

    /**
     * needs to be defined
     */
    protected string $extension = '';

    /**
     * @throws ReadParseException
     * @throws FileSystemNotDefinedException
     */
    public function __construct(?string $path = null)
    {
        parent::__construct();
        if (! is_null($path)) {
            $this->path = $path;
        }
        if ($this->path === '') {
            throw new ReadParseException("'public string \$path = ...' not set in ".get_class($this));
        }
        if ($this->extension === '') {
            throw new ReadParseException("'protected string \$extension = ...' not set in ".get_class($this));
        }
        $extension = pathinfo($this->path)['extension'] ?? 'extension not found in path';
        if ($this->extension !== $extension) {
            throw new ReadParseException(sprintf("expected extension '%s' but found '%s'", $this->extension, $extension));
        }
    }

    protected function storageExists(string $path): bool
    {
        return Storage::disk(StorageBase::DISK)->exists($path);
    }

    protected function getStorageRoot(): string
    {
        return rtrim(Storage::disk(StorageBase::DISK)->getConfig()['root'] ?? '', '/').'/';
    }
}
