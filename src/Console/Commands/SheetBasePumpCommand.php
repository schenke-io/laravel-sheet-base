<?php

namespace SchenkeIo\LaravelSheetBase\Console\Commands;

use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;
use SchenkeIo\LaravelSheetBase\Exceptions\ConfigErrorException;

class SheetBasePumpCommand extends Command
{
    protected $signature = 'sheet-base:pump';

    protected $description = 'pump data within each pipeline';

    /**
     * @throws ConfigErrorException
     */
    public function handle(): void
    {
        $inform = function (string $txt) {
            $this->info($txt);
        };
        $config = SheetBaseConfig::make();
        foreach ($config->pipelines as $name => $pipeline) {
            $pipeline->pump($inform, $name, self::class);
        }
    }
}
