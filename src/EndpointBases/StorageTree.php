<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use Throwable;

abstract class StorageTree extends StorageBase implements IsEndpoint
{
    /**
     * root dir for the files to write
     */
    public string $root = '';

    /**
     * list of file-bases (made from keys) which are OK to be written
     */
    public array $fileBases = [];

    /**
     * @throws EndpointCodeException
     * @throws FileSystemNotDefinedException
     * @throws Throwable
     */
    public function __construct()
    {
        parent::__construct();
        $className = class_basename($this);
        if ($this->root === '') {
            throw new EndpointCodeException($className, "'public string \$root = ...' not set in ".get_class($this));
        }
        if (count($this->fileBases) == 0) {
            throw new EndpointCodeException($className, "'public array \$fileBases = [...]' not set in ".get_class($this));
        }
    }

    public function toString(): string
    {
        return $this->root.'/***';
    }
}
