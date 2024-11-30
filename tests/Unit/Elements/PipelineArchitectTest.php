<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Elements;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\PipelineArchitect;
use SchenkeIo\LaravelSheetBase\Exceptions\ArchitectureException;

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

    public function test_method_not_defined()
    {
        $pa = new PipelineArchitect;
        $pa->columnMethods = ['unbekannt'];
        $this->expectException(ArchitectureException::class);
        $pa->scan();
    }

    public function test_endpoint_missing()
    {
        $pa = new PipelineArchitect;
        $pa->readExtensions = ['unbekannt'];
        $this->expectException(ArchitectureException::class);
        $pa->scan();
    }
}
