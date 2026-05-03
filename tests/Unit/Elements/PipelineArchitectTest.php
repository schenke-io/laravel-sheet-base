<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Elements;

use SchenkeIo\LaravelSheetBase\Elements\PipelineArchitect;
use SchenkeIo\LaravelSheetBase\Tests\TestCase;

class PipelineArchitectTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function test_architecture()
    {
        $this->expectNotToPerformAssertions();
        (new PipelineArchitect)->scan();
    }
}
