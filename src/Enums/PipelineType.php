<?php

namespace SchenkeIo\LaravelSheetBase\Enums;

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

/**
 * Enum representing the type of pipeline.
 */
enum PipelineType
{
    case Table;
    case Tree;

    /**
     * Get the default schema for the pipeline type.
     */
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
