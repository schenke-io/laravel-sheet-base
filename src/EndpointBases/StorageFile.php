<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
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
     * @throws ReadParseException
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
        throw_if($this->path === '', new ReadParseException($className, "'public string \$path = ...' not set in ".get_class($this)));
        throw_if($this->extension === '', new ReadParseException($className, "'protected string \$extension = ...' not set in ".get_class($this)));
        $extension = pathinfo($this->path)['extension'] ?? 'extension not found in path';
        throw_unless($this->extension == $extension, new ReadParseException($className, sprintf("expected extension '%s' but found '%s'", $this->extension, $extension)));
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
