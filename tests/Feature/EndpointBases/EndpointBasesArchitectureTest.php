<?php

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;

test('all EndpointBases are abstract')
    ->expect('SchenkeIo\LaravelSheetBase\Elements\EndpointBases')
    ->toBeAbstract();

test('all EndpointBases have IsEndpoint interface')
    ->expect('SchenkeIo\LaravelSheetBase\Elements\EndpointBases')
    ->toImplement(IsEndpoint::class);
