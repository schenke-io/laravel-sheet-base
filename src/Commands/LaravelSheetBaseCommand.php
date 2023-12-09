<?php

namespace SchenkeIo\LaravelSheetBase\Commands;

use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseConfig;

class LaravelSheetBaseCommand extends Command
{
    public $signature = 'sheet-base:check';

    public $description = 'verifies the syntax of /config/sheet-base.php';

    public function handle(): int
    {
        /*
         * do we have the file system 'sheet-base' installed
        */
        if (! is_array(config('filesystems.disks.sheet-base'))) {
            $this->error('the disk "sheet-base" is not configured in /config/filesystems.php');

            return self::FAILURE;
        }
        $this->info("filesystems disk 'sheet-base' defined");
        /*
         * do we have the config file
         */
        $configFileName = SheetBaseConfig::CONFIG_FILE_BASE.'.php';
        if (! is_array(config(SheetBaseConfig::CONFIG_FILE_BASE))) {
            $this->error("config file '/config/$configFileName' empty or do not exists");

            return self::FAILURE;
        }
        $this->info('config file found');

        $result = SheetBaseConfig::checkAndReportError();
        if (! is_null($result)) {
            $this->error($result);

            return self::FAILURE;
        }
        $this->info('no syntax errors in config file');

        return self::SUCCESS;
    }
}
