<?php

namespace SchenkeIo\LaravelSheetBase\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;

/**
 * Command to pump data through each defined pipeline.
 */
class SheetBasePumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheet-base:pump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pump data within each pipeline';

    /**
     * Execute the console command.
     */
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
