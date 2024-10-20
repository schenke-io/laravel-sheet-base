<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Skills\FindEndpointClass;

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
        $this->updateCoverageBadge();

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

    private function updateCoverageBadge(): void
    {
        $dir = realpath(__DIR__.'/../../../../tests/coverage/');
        $coverageTxt = "$dir/coverage.txt";
        $coverageSvg = "$dir/coverage.svg";
        if (! file_exists($coverageTxt)) {
            $this->error("txt file missing: $coverageTxt");

            return;
        }
        $content = file_get_contents($coverageTxt);
        preg_match('@Lines:\s*([\d.]+)%@', $content, $matches);
        $percentage = $matches[1];

        $svg = <<<SVG
<svg width="123.3" height="20" viewBox="0 0 1233 200" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Coverage: $percentage%">
  <title>Coverage: $percentage%</title>
  <linearGradient id="dPVkA" x2="0" y2="100%">
    <stop offset="0" stop-opacity=".1" stop-color="#EEE"/>
    <stop offset="1" stop-opacity=".1"/>
  </linearGradient>
  <mask id="JyxQb"><rect width="1233" height="200" rx="30" fill="#FFF"/></mask>
  <g mask="url(#JyxQb)">
    <rect width="623" height="200" fill="#555"/>
    <rect width="610" height="200" fill="#3C1" x="623"/>
    <rect width="1233" height="200" fill="url(#dPVkA)"/>
  </g>
  <g aria-hidden="true" fill="#fff" text-anchor="start" font-family="Verdana,DejaVu Sans,sans-serif" font-size="110">
    <text x="60" y="148" textLength="523" fill="#000" opacity="0.25">Coverage</text>
    <text x="50" y="138" textLength="523">Coverage</text>
    <text x="678" y="148" textLength="510" fill="#000" opacity="0.25">$percentage%</text>
    <text x="668" y="138" textLength="510">$percentage%</text>
  </g>
  
</svg>
SVG;
        file_put_contents($coverageSvg, $svg);
        unlink($coverageTxt);
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
<th>documentation</th>
<th>reader</th>
<th>writer</th>
</tr>
HTML;

        $endpoints = [];
        foreach (FindEndpointClass::WRITERS as $extension => $class) {
            $endpoints[$extension]['writer'] = class_basename($class);
        }
        foreach (FindEndpointClass::READERS as $extension => $class) {
            $endpoints[$extension]['reader'] = class_basename($class);
        }
        ksort($endpoints);

        foreach ($endpoints as $extension => $columns) {
            $return .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
                $extension,
                self::EXTENSION_HELP[$extension] ?? '-',  // @phpstan-ignore-line
                $columns['reader'] ?? '-',                // @phpstan-ignore-line
                $columns['writer'] ?? '-'                 // @phpstan-ignore-line
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
