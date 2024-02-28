<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Elements;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\PipelineArchitect;

class PipelineArchitectTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function testArchitecture()
    {
        $this->expectNotToPerformAssertions();
        (new PipelineArchitect)->scan();
    }
}
