<?php

namespace SchenkeIo\LaravelSheetBase\Traits;

use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

trait HasFileExtensions
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
     */
    protected function validatePathAndExtension(?string $path = null): void
    {
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
}
