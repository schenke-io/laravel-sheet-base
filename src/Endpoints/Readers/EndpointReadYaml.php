<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\EndpointBases\YamlFileReader;

class EndpointReadYaml extends YamlFileReader
{
    protected string $extension = 'yaml';
}
