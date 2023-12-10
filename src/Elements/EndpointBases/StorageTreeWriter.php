<?php

namespace SchenkeIo\LaravelSheetBase\Elements\EndpointBases;

use Illuminate\Support\Facades\Storage;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

abstract class StorageTreeWriter extends StorageTree implements IsWriter
{
    /**
     * @throws ReadParseException
     */
    protected function storagePut(string $path, string $content): void
    {
        /*
         * $path must be under the root
         */
        if (str_starts_with($path, $this->root)) {
            Storage::disk(self::DISK)->put($path, $content);
        } else {
            throw new ReadParseException(sprintf(
                'the given file %s is not under the given root %s',
                $path, $this->root
            ));
        }
    }

    public function explain(): string
    {
        return 'writes under this root '.$this->root;
    }
}
