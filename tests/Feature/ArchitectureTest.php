<?php

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;

test('debug functions not used anymore')
    ->expect(['dd', 'dump', 'ddd', 'ray'])
    ->not->toBeUsed();

test('eval only used in tests')
    ->expect(['eval'])
    ->not->toBeUsedIn('SchenkeIo\LaravelSheetBase');

test('endpoints are extended')
    ->expect('App\Endpoints')
    ->toImplement(IsEndpoint::class);
