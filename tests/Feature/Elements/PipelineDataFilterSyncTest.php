<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\Elements\PipelineType;
use SchenkeIo\LaravelSheetBase\Tests\TestCase;

class PipelineDataFilterSyncTest extends TestCase
{
    public function test_new_from_type(): void
    {
        $pld = PipelineData::fromType(PipelineType::Table);
        $this->assertInstanceOf(PipelineData::class, $pld);
    }
}
