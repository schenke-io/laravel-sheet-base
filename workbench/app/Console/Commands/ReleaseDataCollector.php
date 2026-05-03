<?php

namespace Workbench\App\Console\Commands;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Enums\ColumnType;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;

class ReleaseDataCollector
{
    public const COLUMN_HELP = [
        'addId' => 'numeric or string id',
        'addDot' => "text id with dots like 'alpha.beta.gamma'",
        'addLanguage' => 'valid language code as name',
        'addClosure' => 'allows for custom formatting function using column name and data row as input',
    ];

    public const EXTENSION_HELP = [
        'csv' => 'comma seperated values',
        'json' => 'JavaScript Object Notation',
        'neon' => 'Nette Object Notation',
        'php' => 'PHP config file',
        'psv' => 'pipe seperated values',
        'tsv' => 'tab seperated values',
        'txt' => 'text files with just keys one per line',
        'yaml' => 'YAML config file',
        'yml' => 'YAML config file',
    ];

    public function getEndpointTable(): array
    {
        $data[] = ['extension', 'documentation', 'reader', 'writer'];
        $endpoints = [];
        foreach (FindEndpointClass::getWriters() as $extension => $class) {
            $endpoints[$extension]['writer'] = class_basename($class);
        }
        foreach (FindEndpointClass::getReaders() as $extension => $class) {
            $endpoints[$extension]['reader'] = class_basename($class);
        }
        ksort($endpoints);

        foreach ($endpoints as $extension => $columns) {
            $data[] = [
                $extension,
                self::EXTENSION_HELP[$extension] ?? '-',
                $columns['reader'] ?? '-',
                $columns['writer'] ?? '-',
            ];
        }

        return $data;
    }

    public function getColumnsTable(): array
    {
        $data[] = ['method', 'definition', 'can be null', 'is ID'];
        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void {}
        };
        $reflection = new \ReflectionClass(SheetBaseSchema::class);
        foreach ($reflection->getMethods() as $method) {
            $methodName = $method->name;
            if (preg_match('@^add(.*)$@', $methodName, $matches)) {
                [$methodName, $typeName] = $matches;
                // add a column
                $schema->$methodName('id');
                // get its schema
                $columnSchema = $schema->columns['id'];
                // get column type
                $columnType = ColumnType::from($typeName);
                // does it preserve null
                $canBeNull = is_null($columnType->format(null));
                // checkForId
                $isId = $columnType->isId();

                $data[] = [
                    $methodName,
                    self::COLUMN_HELP[$methodName] ?? '-',
                    $canBeNull ? 'yes' : 'no',
                    $isId ? 'yes' : 'no',
                ];
                $schema->columns = [];
                $schema->idName = '';

            }
        }

        return $data;
    }
}
