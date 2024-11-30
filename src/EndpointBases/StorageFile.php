<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use Throwable;

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
     * @throws EndpointCodeException
     * @throws FileSystemNotDefinedException
     * @throws Throwable
     */
    public function __construct(?string $path = null)
    {
        parent::__construct();
        if (! is_null($path)) {
            $this->path = $path;
        }
        $className = class_basename($this);
        if ($this->path === '') {
            throw new EndpointCodeException($className, "'public string \$path = ...' not set in ".get_class($this));
        }
        if ($this->extension === '') {
            throw new EndpointCodeException($className, "'protected string \$extension = ...' not set in ".get_class($this));
        }
        $extension = pathinfo($this->path)['extension'] ?? 'extension not found in path';
        if ($this->extension != $extension) {
            throw new EndpointCodeException($className, sprintf("expected extension '%s' but found '%s'", $this->extension, $extension));
        }
    }

    public function toString(): string
    {
        return $this->path;
    }

    protected function storageExists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    protected function getStorageRoot(): string
    {
        return rtrim(Storage::disk($this->disk)->getConfig()['root'] ?? '', '/').'/';
    }
}
