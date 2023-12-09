<?php

test('only interfaces in this directory')
    ->expect('SchenkeIo\LaravelSheetBase\Contracts')
    ->toBeInterfaces();
