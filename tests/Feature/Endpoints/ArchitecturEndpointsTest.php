<?php

use SchenkeIo\LaravelSheetBase\Contracts\IsEndpoint;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Contracts\IsWriter;

test('all endpoints are not abstract or final')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints')
    ->not()->toBeAbstract()->not()->toBeFinal();

test('all endpoints have IsEndpoint interface')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints')
    ->toImplement(IsEndpoint::class);

test('all endpoints start with Endpoint')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints')
    ->toHavePrefix('Endpoint');

test('all writers have IsWriter interface')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints\Writers')
    ->toImplement(IsWriter::class);

test('all readers have IsReader interface')
    ->expect('SchenkeIo\LaravelSheetBase\Endpoints\Readers')
    ->toImplement(IsReader::class);
