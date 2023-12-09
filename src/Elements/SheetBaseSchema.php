<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

/**
 * @method void addId(string $name = 'id')
 * @method void addDot(string $name = 'id')
 * @method void addString(string $name)
 * @method void addUnsigned(string $name)
 * @method void addLanguage(string $name)
 * @method void addFloat(string $name)
 */
abstract class SheetBaseSchema
{
    protected string $idName = '';

    /** @var array<string,ColumnType> */
    protected array $columns = [];

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
     * @return array<string,ColumnType>
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
        return $this->columns[$this->idName]->getPipelineType();
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
            if ($column == ColumnType::Language) {
                $langCount++;
            } elseif ($column == ColumnType::Dot) {
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

    /**
     * @throws SchemaDefinitionException
     */
    public function __call(string $name, array $arguments)
    {
        if (preg_match('@^add(.*)$@', $name, $matches)) {
            $schemaColumn = ColumnType::tryFrom($matches[1]);
            if (is_null($schemaColumn)) {
                throw new SchemaDefinitionException('column type unknown: '.$name);
            }
            $this->addColumn($arguments, $schemaColumn);
        } else {
            throw new SchemaDefinitionException('unknown method: '.$name);
        }
    }

    /**
     * @throws SchemaDefinitionException
     */
    private function addColumn(array $arguments, ColumnType $schemaColumn): void
    {
        $name = $schemaColumn->getName($arguments);
        if ($name == '') {
            throw new SchemaDefinitionException('column name cannot be empty string');
        }
        if ($schemaColumn->isId()) {
            if ($this->idName == '') {
                $this->idName = $name;
            } elseif ($this->idName != $name) {
                throw new SchemaDefinitionException('only one id column is possible');
            }
        }
        if (isset($this->columns[$name])) {
            throw new SchemaDefinitionException("column name '$name' is already in use");
        }
        if ($schemaColumn == ColumnType::Language) {
            if (strlen($name) != 2) {
                throw new SchemaDefinitionException("column name '$name' is defined as language and has not a 2-char name");
            }
        }
        $this->columns[$name] = $schemaColumn;

    }
}
