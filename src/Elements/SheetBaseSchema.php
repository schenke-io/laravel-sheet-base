<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Elements\Columns\ControlColumns;
use SchenkeIo\LaravelSheetBase\Elements\Columns\NumericColumns;
use SchenkeIo\LaravelSheetBase\Elements\Columns\TextColumns;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

abstract class SheetBaseSchema
{
    use ControlColumns;
    use NumericColumns;
    use TextColumns;

    protected string $idName = '';

    /** @var array<string,ColumnSchema> */
    public array $columns = [];

    /**
     * @throws SchemaDefinitionException
     */
    public function __construct()
    {
        $this->define();
        $this->verify();
    }

    /**
     * define the schema in Laravel migration syntax
     */
    abstract protected function define(): void;

    /**
     * @throws SchemaDefinitionException
     */
    private function addColumn(string $name, ColumnSchema $columnDefinition): void
    {
        if ($name == '') {
            throw new SchemaDefinitionException('column name cannot be empty string');
        }
        if ($columnDefinition->type->isId()) {
            if ($this->idName == '') {
                $this->idName = $name;
            } elseif ($this->idName != $name) {
                throw new SchemaDefinitionException('only one id column is possible');
            }
        }
        if (isset($this->columns[$name])) {
            throw new SchemaDefinitionException("column name '$name' is already in use");
        }
        if ($columnDefinition->type == ColumnType::Language) {
            if (strlen($name) != 2) {
                throw new SchemaDefinitionException("column name '$name' is defined as language and has not a 2-char name");
            }
        }
        $this->columns[$name] = $columnDefinition;
    }

    /**
     * @return array<string,ColumnSchema>
     */
    public function getColumns(): array
    {
        return $this->columns;

    }

    public function getIdName(): string
    {
        return $this->idName;
    }

    public function getPipelineType(): PipelineType
    {
        return $this->columns[$this->idName]->type->getPipelineType();
    }

    /**
     * @throws SchemaDefinitionException
     */
    public function verify(): void
    {
        if ($this->idName == '') {
            throw new SchemaDefinitionException('id column not defined');
        }
        if (count($this->columns) < 2) {
            $columnNames = implode(', ', array_keys($this->columns));
            throw new SchemaDefinitionException('2 columns minimum required but found only one: '.$columnNames);
        }
        /*
         * if one column is language we can have only one column dot key and the other must be language
         */
        $columnCount = 0;
        $dotKeyCount = 0;
        $langCount = 0;
        foreach ($this->columns as $column) {
            if ($column->type == ColumnType::Language) {
                $langCount++;
            } elseif ($column->type == ColumnType::Dot) {
                $dotKeyCount++;
            }
            $columnCount++;
        }
        if ($langCount > 0) {
            if ($dotKeyCount != 1 || $langCount + 1 !== $columnCount) {
                throw new SchemaDefinitionException(sprintf(
                    'from %d columns we found %d language and %d dotKey column but expected %d langauge columns and 1 dotKey',
                    $columnCount, $langCount, $dotKeyCount, $columnCount - 1
                ));
            }
        }
    }
}
