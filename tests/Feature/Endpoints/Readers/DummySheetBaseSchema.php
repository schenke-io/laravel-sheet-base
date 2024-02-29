<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

class DummySheetBaseSchema extends SheetBaseSchema
{
    protected function define(): void
    {
        $this->addId('a');
        $this->addUnsigned('b');
    }
}
