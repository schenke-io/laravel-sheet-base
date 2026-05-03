<?php

namespace Workbench\App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use SchenkeIo\PackagingTools\Badges\MakeBadge;
use SchenkeIo\PackagingTools\Enums\BadgeStyle;
use SchenkeIo\PackagingTools\Markdown\MarkdownAssembler;

class MakeReleaseCommand extends Command
{
    protected $signature = 'make:release';

    protected $description = 'make data needed for a package release';

    public function handle(ReleaseDataCollector $collector): void
    {
        try {
            $mda = new MarkdownAssembler('workbench/resources/md');

            $mda->addMarkdown('header.md');

            $badges = $mda->badges();
            $badges->version(BadgeStyle::FlatSquare);
            $badges->test('run-tests.yml', BadgeStyle::FlatSquare);
            $badges->download(BadgeStyle::FlatSquare);

            MakeBadge::makeCoverageBadge('build/logs/clover.xml')
                ->store('.github/coverage.svg', BadgeStyle::FlatSquare);
            $badges->local('Coverage', '.github/coverage.svg');

            MakeBadge::makePhpStanBadge('phpstan.neon')
                ->store('.github/phpstan.svg', BadgeStyle::FlatSquare);
            $badges->local('PHPStan', '.github/phpstan.svg');

            $mda->addMarkdown('introduction.md');
            $mda->addTableOfContents();
            $mda->addMarkdown('pipelines.md');
            $mda->addMarkdown('installation.md');
            $mda->addMarkdown('configuration1.md');
            $mda->tables()->fromArray($collector->getColumnsTable());
            $mda->addMarkdown('configuration2.md');
            $mda->addMarkdown('endpoints.md');
            $mda->addMarkdown('endpoint_files.md');
            $mda->tables()->fromArray($collector->getEndpointTable());
            $mda->addMarkdown('endpoint_array.md');
            $mda->addMarkdown('endpoint_language.md');
            $mda->addMarkdown('endpoint_google.md');
            $mda->addMarkdown('closing.md');

            $mda->writeMarkdown('README.md');

            $this->info('README.md and badges updated successfully.');

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
