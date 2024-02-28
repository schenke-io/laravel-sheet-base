<?php

namespace SchenkeIo\LaravelSheetBase\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;

class SheetBasePumpCommand extends Command
{
    protected $signature = 'sheet-base:pump';

    protected $description = 'pump data within each pipeline';

    public function handle(): int
    {
        $inform = function (string $txt) {
            $this->info($txt);
        };
        try {
            $config = SheetBaseConfig::make();
            foreach ($config->pipelines as $name => $pipeline) {
                $pipeline->pump($inform, $name, class_basename(self::class));
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
