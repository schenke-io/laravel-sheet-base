<?php

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;

test('all endpoints are not abstract or final')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints')
    ->not()->toBeAbstract()->not()->toBeFinal();

test('all endpoints have IsEndpoint interface')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints')
    ->toImplement(IsEndpoint::class);

test('all endpoints start with Endpoint')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints')
    ->toHavePrefix('Endpoint');
