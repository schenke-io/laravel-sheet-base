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

        try {
            $config = SheetBaseConfig::make();
            $pipelines = $config->pipelines;
            ksort($pipelines);
            foreach ($pipelines as $name => $pipeline) {
                $pipeline->pump($this, $name, class_basename(self::class));
            }
        } catch (Exception $e) {
            $this->error(sprintf('line %d in %s => %s', $e->getLine(), basename($e->getFile()), $e->getMessage()));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
