<?php

namespace SchenkeIo\LaravelSheetBase\Traits;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;

trait HasStorage
{
    public const DEFAULT_DISK = 'sheet-base';

    public string $disk = self::DEFAULT_DISK;

    /**
     * @throws FileSystemNotDefinedException
     */
    protected function checkDisk(): void
    {
        if (! is_array(config('filesystems.disks.'.$this->disk))) {
            throw new FileSystemNotDefinedException(
                "the file system disk '{$this->disk}' is not defined in /config/filesystems.php"
            );
        }
    }

    protected function storageExists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    protected function getStorageRoot(): string
    {
        return rtrim(Storage::disk($this->disk)->getConfig()['root'] ?? '', '/').'/';
    }

    protected function storageGet(string $path): ?string
    {
        return Storage::disk($this->disk)->get($path);
    }

    protected function storagePut(string $path, string $content): void
    {
        Storage::disk($this->disk)->put($path, $content);
    }
}
