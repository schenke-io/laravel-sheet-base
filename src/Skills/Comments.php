<?php

namespace SchenkeIo\LaravelSheetBase\Skills;

trait Comments
{
    /**
     * Standardized comment block generation.
     */
    protected function getComment(string $start, string $writer): string
    {
        $line = str_repeat('=', 60);

        return <<<EOM
$start $line
$start
$start auto-written by $writer
$start
$start do not edit manually
$start
$start $line

EOM;
    }
}
