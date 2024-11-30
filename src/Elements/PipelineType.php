<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

enum PipelineType
{
    case Table;
    case Tree;

    public function getSchema(): SheetBaseSchema
    {
        return match ($this) {
            self::Table => new class extends SheetBaseSchema
            {
                protected function define(): void
                {
                    $this->addId();
                }
            },
            self::Tree => new class extends SheetBaseSchema
            {
                protected function define(): void
                {
                    $this->addDot();
                }
            },
        };
    }
}
