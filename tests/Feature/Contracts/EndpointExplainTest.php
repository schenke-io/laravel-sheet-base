<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Contracts;

use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;
use Workbench\App\Endpoints\LangWrite;
use Workbench\App\Endpoints\PersonsReadPsv;
use Workbench\App\Endpoints\PersonsWriteNeon;

class EndpointExplainTest extends ConfigTestCase
{
    public function testExplain(): void
    {
        $this->assertIsString((new PersonsReadPsv)->explain());
        $this->assertIsString((new PersonsWriteNeon)->explain());
        $this->assertIsString((new LangWrite)->explain());

    }
}
