<?php

test('debug functions')
    ->expect(['dd', 'dump'])
    ->not->toBeUsed();

test('eval only in tests')
    ->expect(['eval'])
    ->not->toBeUsedIn('SchenkeIo\LaravelSheetBase');
