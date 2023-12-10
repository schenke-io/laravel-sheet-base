<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Contracts;

use SchenkeIo\LaravelSheetBase\Tests\data\LangWrite;
use SchenkeIo\LaravelSheetBase\Tests\data\PersonsReadPsv;
use SchenkeIo\LaravelSheetBase\Tests\data\PersonsWriteNeon;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class EndpointExplainTest extends ConfigTestCase
{
    public function testExplain(): void
    {
        $this->assertIsString((new PersonsReadPsv)->explain());
        $this->assertIsString((new PersonsWriteNeon)->explain());
        $this->assertIsString((new LangWrite)->explain());

    }
}
