<?php

namespace SchenkeIo\LaravelSheetBase\Elements\EndpointBases;

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Exceptions\FileSystemNotDefinedException;
use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

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
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->root === '') {
            throw new ReadParseException("'public string \$root = ...' not set in ".get_class($this));
        }
        if (count($this->fileBases) == 0) {
            throw new ReadParseException("'public array \$fileBases = [...]' not set in ".get_class($this));
        }
    }
}
