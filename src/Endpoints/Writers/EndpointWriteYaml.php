<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use SchenkeIo\LaravelSheetBase\EndpointBases\YamlFileWriter;

class EndpointWriteYaml extends YamlFileWriter
{
    protected string $extension = 'yaml';
}
