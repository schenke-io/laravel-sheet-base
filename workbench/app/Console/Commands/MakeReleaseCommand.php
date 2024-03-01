<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

class MakeReleaseCommand extends Command
{
    protected $signature = 'make:release';

    protected $description = 'make data needed for a package release';

    protected const COLUMN_HELP = [
        'addId' => 'numeric or string id',
        'addDot' => "text id with dots like 'alpha.beta.gamma'",
        'addLanguage' => 'valid language code as name',
        'addClosure' => 'allows for custom formatting function using column name and data row as input',
    ];

    protected const EXTENSION_HELP = [
        'neon' => 'Nette Object Notation',
        'csv' => 'comma seperated values',
        'psv' => 'pipe seperated values',
        'tsv' => 'tab seperated values',
        'json' => 'JavaScript Object Notation',
        'php' => 'PHP config file',
    ];

    protected const PROPERTY_HELP = [
        'path' => 'filepath within the disk',
        'disk' => 'name of filesystem disk',
        'spreadsheetId' => 'id of the Google spreadsheet found in its URL',
        'sheetName' => 'name of the sheet within the spreadsheet',
        'fileBases' => 'which first parts of the dot-keys should result in files',
        'root' => 'langugae directory in the disk',
    ];

    /**
     * @throws \ReflectionException
     */
    public function handle(): void
    {
        $content = '';
        foreach (file(__DIR__.'/../../../docs/README.md') as $line) {
            if (preg_match('@_+include\((.*)\)@', $line, $matches)) {
                [$all, $key] = $matches;
                $line = match ($key) {
                    'warning' => $this->getWarningRemark(),
                    'table_columns' => $this->getColumnsTable(),
                    'table_endpoints' => $this->getEndpointTable(),
                    default => $line
                };
            }
            $content .= $line;
        }
        file_put_contents(__DIR__.'/../../../../README.md', $content);
        $this->info('readme made');
    }

    /**
     * @throws \ReflectionException
     */
    protected function getEndpointTable(): string
    {
        $return = <<<'HTML'
<table>
<tr>
<th>extension</th>
<th>description</th>
<th>reader</th>
<th>writer</th>
</tr>
HTML;

        $endpoints = [];
        foreach (glob(realpath(__DIR__.'/../../../../src').'/Endpoints/**/*.php') as $file) {
            if (preg_match('@(/Endpoints/.*/.*)\.php@', $file, $matches)) {
                [$all, $path] = $matches;
                $class = str_replace('/', '\\', 'SchenkeIo/LaravelSheetBase'.$path);
                $reflection = new \ReflectionClass($class);
                $shortName = $reflection->getShortName();
                $readWrite = preg_match('/Write/', $shortName) ? 'Writer' : 'Reader';
                $properties = [];
                $extension = '';
                foreach ($reflection->getProperties() as $property) {
                    if ($property->isPublic()) {
                        if (in_array($property->getType(), ['string', 'array'])) {
                            $properties[$property->name] = (string) $property->getType();
                        }
                    } elseif ($property->name == 'extension') {
                        $extension = $property->getDefaultValue();
                    }
                }
                if (! $extension) {
                    continue;
                }
                $endpoints[$extension][$readWrite] = $shortName;
            }
        }
        ksort($endpoints);
        foreach ($endpoints as $extension => $columns) {
            $return .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
                $extension,
                self::EXTENSION_HELP[$extension] ?? '-',
                $columns['Reader'] ?? '-',
                $columns['Writer'] ?? '-'
            );
        }
        $return .= '</table>';

        return $return;
    }

    protected function getColumnsTable(): string
    {
        $return = <<<'HTML'
<table>
<tr>
<th>method</th>
<th>definition</th>
<th>can be null</th>
<th>is ID</th>
</tr>
HTML;

        $schema = new class extends SheetBaseSchema
        {
            protected function define(): void
            {
            }
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
                // does it preserves null
                $canBeNull = is_null($columnType->format(null));
                // checkForId
                $isId = $columnType->isId();

                $return .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
                    $methodName,
                    self::COLUMN_HELP[$methodName] ?? '-',
                    $canBeNull ? 'yes' : 'no',
                    $isId ? 'yes' : 'no'
                );
                $schema->columns = [];
                $schema->idName = '';

            }
        }
        $return .= '</table>';

        return $return;
    }

    private function getWarningRemark(): string
    {
        return <<<'HTML'
<!-- 


This file is generated from /workbench/docs/README.md 

All edits in the file /README.md will be overwritten

-->
HTML;

    }
}
