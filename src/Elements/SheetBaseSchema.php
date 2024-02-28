<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Elements\Columns\ControlColumns;
use SchenkeIo\LaravelSheetBase\Elements\Columns\NumericColumns;
use SchenkeIo\LaravelSheetBase\Elements\Columns\TextColumns;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;

abstract class SheetBaseSchema
{
    use ControlColumns;
    use NumericColumns;
    use TextColumns;

    protected string $idName = '';

    protected PipelineType $pipelineType = PipelineType::Table;

    /** @var array<string,ColumnSchema> */
    public array $columns = [];

    /**
     * @throws SchemaAddColumnException
     */
    public function __construct()
    {
        $this->define();
    }

    /**
     * define the schema in Laravel migration syntax
     *
     * @throws SchemaAddColumnException
     */
    abstract protected function define(): void;

    /**
     * @throws SchemaAddColumnException
     */
    private function addColumn(string $name, ColumnSchema $columnDefinition): void
    {
        $track = debug_backtrace(0, 4);
        $source = sprintf('class %s line %d', $track[2]['class'], $track[2]['line']);

        if ($name == '') {
            throw new SchemaAddColumnException($source, 'column name cannot be empty string');
        }
        if ($columnDefinition->type->isId()) {
            if ($this->idName == '') {
                // first time
                $this->idName = $name;
                $this->pipelineType = $columnDefinition->type->getPipelineType();
            } else {
                // second time
                throw new SchemaAddColumnException($source, 'only one id column is possible');
            }
            if (count($this->columns) > 0) {
                throw new SchemaAddColumnException($source, 'id column must be the first');
            }
        }
        if (isset($this->columns[$name])) {
            throw new SchemaAddColumnException($source, "column name '$name' is already in use");
        }
        if ($columnDefinition->type == ColumnType::Language) {
            if (strlen($name) != 2) {
                throw new SchemaAddColumnException($source, "column name '$name' is defined as language and has not a 2-char name");
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
        return $this->pipelineType;
    }

    /**
     * @throws SchemaVerifyColumnsException
     */
    public function verify(string $identifier): void
    {
        /*
         * if one column is language we can have only one column dot key and the other must be language
         */
        $columnCount = 0;
        $dotKeyCount = 0;
        $langCount = 0;
        $idCount = 0;
        foreach ($this->columns as $column) {
            if ($column->type->isId()) {
                $idCount++;
            }
            if ($column->type == ColumnType::Language) {
                $langCount++;
            } elseif ($column->type == ColumnType::Dot) {
                $dotKeyCount++;
            }
            $columnCount++;
        }
        if ($langCount > 0) {
            if ($dotKeyCount != 1 || $langCount + 1 !== $columnCount) {
                throw new SchemaVerifyColumnsException($identifier, sprintf(
                    'schema > from %d columns we found %d language and %d dotKey column but expected %d langauge columns and 1 dotKey',
                    $columnCount, $langCount, $dotKeyCount, $columnCount - 1
                ));
            }
        }
        if ($columnCount == 0) {
            throw new SchemaVerifyColumnsException($identifier, 'schema > no column defined');
        } elseif ($columnCount > 1) {
            // one must be id
            if ($idCount == 0) {
                throw new SchemaVerifyColumnsException($identifier, 'schema > id column not defined');
            }
        }
    }
}
