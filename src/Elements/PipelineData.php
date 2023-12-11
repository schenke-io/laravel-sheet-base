<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Exceptions\ReadParseException;

/**
 * generic data structure used inside the pipeline
 */
final class PipelineData
{
    protected array $table = [];

    protected string $idName = 'id';

    protected PipelineType $pipelineType;

    public function __construct(public SheetBaseSchema $sheetBaseSchema)
    {
        $this->idName = $this->sheetBaseSchema->getIdName();
        $this->pipelineType = $this->sheetBaseSchema->getPipelineType();
    }

    public static function fromArray(array $data, SheetBaseSchema $sheetBaseSchema): PipelineData
    {
        $me = new PipelineData($sheetBaseSchema);
        $me->table = $data;

        return $me;
    }

    public function addRow(array $row): void
    {
        if ($this->idName != '') {
            // with id
            $id = $row[$this->idName];
            unset($row[$this->idName]);
            if (strlen($id) < 1) {
                throw new ReadParseException('empty id field');
            }
            foreach ($this->sheetBaseSchema->getColumns() as $columnName => $columnDefinition) {
                if ($columnName == $this->idName) {
                    continue;
                }
                $cellValue = $columnDefinition->format($row[$columnName] ?? null, $row);
                if ($this->pipelineType == PipelineType::Tree) {
                    data_set($this->table, "$id.$columnName", $cellValue);
                } else {
                    $this->table[$id][$columnName] = $cellValue;
                }
            }
        } else {
            // without id
            foreach ($this->sheetBaseSchema->getColumns() as $columnName => $columnDefinition) {
                $cellValue = $columnDefinition->format($row[$columnName] ?? null, $row);
                $this->table[$columnName][] = $cellValue;
            }
        }
    }

    public function toArray(): array
    {
        return $this->table;
    }
}
