<?php

namespace SchenkeIo\LaravelSheetBase\Skills;

trait Comments
{
    protected function getComment(string $start, string $writer): string
    {
        return <<<EOM
$start ===========================================================
$start 
$start auto written by $writer 
$start 
$start do not edit manually
$start 
$start ===========================================================

EOM;

    }
}
