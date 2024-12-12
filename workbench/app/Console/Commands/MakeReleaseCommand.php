<?php

namespace Workbench\App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;
use SchenkeIo\PackagingTools\Badges\BadgeStyle;
use SchenkeIo\PackagingTools\Badges\MakeBadge;
use SchenkeIo\PackagingTools\Markdown\MarkdownAssembler;

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

    protected const PROPERTY_HELP = [
        'path' => 'filepath within the disk',
        'disk' => 'name of filesystem disk',
        'spreadsheetId' => 'id of the Google spreadsheet found in its URL',
        'sheetName' => 'name of the sheet within the spreadsheet',
        'fileBases' => 'which first parts of the dot-keys should result in files',
        'root' => 'language directory in the disk',
    ];

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {

        try {
            $mda = new MarkdownAssembler('workbench/resources/md');
            $mda->addMarkdown('header.md');
            $mda->addMarkdown('introduction.md');
            $mda->addTableOfContents();
            $mda->addMarkdown('pipelines.md');
            $mda->addMarkdown('installation.md');
            $mda->addMarkdown('configuration1.md');
            $mda->addTableFromArray($this->getColumnsTable());
            // columns
            $mda->addMarkdown('configuration2.md');
            $mda->addMarkdown('endpoints.md');
            $mda->addMarkdown('endpoint_files.md');
            $mda->addTableFromArray($this->getEndpointTable());
            $mda->addMarkdown('endpoint_array.md');
            $mda->addMarkdown('endpoint_language.md');
            $mda->addMarkdown('endpoint_google.md');
            $mda->addMarkdown('closing.md');

            $mda->writeMarkdown('README.md');

        } catch (Exception $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->updateCoverageBadge();
        $this->updatePhpStanBadge();
    }

    /**
     * @throws FileNotFoundException
     */
    private function updateCoverageBadge(): void
    {
        $badge = MakeBadge::makeCoverageBadge('build/logs/clover.xml', '44cc11');
        $badge->store('.github/coverage.svg', BadgeStyle::FlatSquare);
    }

    /**
     * @throws FileNotFoundException
     */
    private function updatePhpStanBadge(): void
    {
        $badge = MakeBadge::makePhpStanBadge('phpstan.neon');
        $badge->store('.github/phpstan.svg', BadgeStyle::FlatSquare);
    }

    protected function getEndpointTable(): array
    {
        $data[] = ['extension', 'documentation', 'reader', 'writer'];
        $endpoints = [];
        foreach (FindEndpointClass::WRITERS as $extension => $class) {
            $endpoints[$extension]['writer'] = class_basename($class);
        }
        foreach (FindEndpointClass::READERS as $extension => $class) {
            $endpoints[$extension]['reader'] = class_basename($class);
        }
        ksort($endpoints);

        foreach ($endpoints as $extension => $columns) {
            $data[] = [
                $extension,
                self::EXTENSION_HELP[$extension] ?? '-',  // @phpstan-ignore-line
                $columns['reader'] ?? '-',                // @phpstan-ignore-line
                $columns['writer'] ?? '-',                 // @phpstan-ignore-line
            ];
        }

        return $data;
    }

    protected function getColumnsTable(): array
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
