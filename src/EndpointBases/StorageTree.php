<?php

namespace SchenkeIo\LaravelSheetBase\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;
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
     * @throws ReadParseException
     * @throws FileSystemNotDefinedException
     * @throws Throwable
     */
    public function __construct()
    {
        parent::__construct();
        $className = class_basename($this);
        throw_if($this->root === '', new ReadParseException($className, "'public string \$root = ...' not set in ".get_class($this)));
        throw_if(count($this->fileBases) == 0, new ReadParseException($className, "'public array \$fileBases = [...]' not set in ".get_class($this)));
    }
}
