<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Exceptions\DataReadException;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

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

    /**
     * @throws EndpointCodeException
     */
    public function addRow(array $row): void
    {
        $id = '';
        if (isset($row[$this->idName])) {
            $id = $row[$this->idName];
            unset($row[$this->idName]);
        }
        if (strlen($id) < 1) {
            throw new DataReadException('empty id field in row: '.count($this->table));
        }
        /*
         * we have a valid id
         */
        foreach ($this->sheetBaseSchema->getColumns() as $columnName => $columnDefinition) {
            // we still have the id column
            if ($columnName == $this->idName) {
                continue;
            }
            /*
             * what is the default value if not null
             */
            $nullValue = $columnDefinition->type->format(null);
            if ($this->pipelineType == PipelineType::Tree) {
                $key = "$id.$columnName";
                if (isset($row[$columnName])) {
                    $cellValue = $columnDefinition->format($columnName, $row);
                } else {
                    $cellValue = data_get($this->table, $key, $nullValue);
                }
                data_set($this->table, $key, $cellValue);
            } else {
                if (isset($row[$columnName])) {
                    $cellValue = $columnDefinition->format($columnName, $row);
                } else {
                    $cellValue = $this->table[$id][$columnName] ?? $nullValue;
                }
                $this->table[$id][$columnName] = $cellValue;
            }
        }
    }

    public function toArray(): array
    {
        return $this->table;
    }
}
