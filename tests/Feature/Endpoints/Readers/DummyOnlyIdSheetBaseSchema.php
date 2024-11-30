<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Endpoints\Readers;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

class DummyOnlyIdSheetBaseSchema extends SheetBaseSchema
{
    protected function define(): void
    {
        $this->addId();
    }
}
