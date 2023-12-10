<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Elements\PipelineType;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Tests\Feature\ConfigTestCase;

class ColumnTypeTest extends ConfigTestCase
{
    public static function dataProviderColumnTypes(): array
    {
        $return = [];
        foreach (ColumnType::cases() as $case) {
            $return[$case->name] = [$case];
        }

        return $return;
    }

    /**
     * @dataProvider dataProviderColumnTypes
     */
    public function testAllMethodsAreDefined(ColumnType $columnType): void
    {
        $reflection = new \ReflectionClass(SheetBaseSchema::class);
        $comment = $reflection->getDocComment();
        $this->assertStringContainsStringIgnoringLineEndings('add'.$columnType->value.'(string', $comment);
        $this->assertInstanceOf(PipelineType::class, $columnType->getPipelineType());
        $this->assertIsString($columnType->getName([]));
        $this->assertEquals('verySpecial', $columnType->getName(['verySpecial']));
        if (in_array($columnType->name, ['ID', 'Dot'])) {
            $this->assertTrue($columnType->isId());
        } else {
            $this->assertFalse($columnType->isId());
        }
    }

    public static function dataProviderFormat(): array
    {
        return [
            'id numeric' => [ColumnType::ID, 1, 1],
            'id string' => [ColumnType::ID, 'h12', 'h12'],
            'dot' => [ColumnType::Dot, 'test.something', 'test.something'],
            'string' => [ColumnType::String, 'test something', 'test something'],
            'unsigned null' => [ColumnType::Unsigned, null, null],
            'unsigned decimal positiv' => [ColumnType::Unsigned, 3, 3],
            'unsigned decimal negativ' => [ColumnType::Unsigned, -3, 3],
            'unsigned string 1' => [ColumnType::Unsigned, '3', 3],
            'unsigned string 2' => [ColumnType::Unsigned, '  3', 3],
            'unsigned string 3' => [ColumnType::Unsigned, '  3    ', 3],
            'unsigned string 4' => [ColumnType::Unsigned, '  3  Test  ', 3],
            'unsigned string 5' => [ColumnType::Unsigned, '  3   5  Test  ', 3],
            'unsigned string 6' => [ColumnType::Unsigned, ' a b c ', 0],
            'unsigned string 7' => [ColumnType::Unsigned, '', null],
            'float 1' => [ColumnType::Float, '', null],
            'float 2' => [ColumnType::Float, null, null],
            'float 3' => [ColumnType::Float, ' 0.123 ', 0.123],
            'float 4' => [ColumnType::Float, '1,23', 1.23],
            'float 5' => [ColumnType::Float, ' 1,23 Euro ', 1.23],
            'float 6' => [ColumnType::Float, 0.123, 0.123],
            'float 7' => [ColumnType::Float, ' 1.234,56', 1234.56],
            'float 8' => [ColumnType::Float, ' 1,234.56', 1234.56],
            'float 9' => [ColumnType::Float, '  1234 ', 1234.0],
            'bool 1' => [ColumnType::Boolean, null, null],
            'bool 2' => [ColumnType::Boolean, true, true],
            'bool 3' => [ColumnType::Boolean, false, false],
            'bool 4' => [ColumnType::Boolean, 'false', false],
            'bool 5' => [ColumnType::Boolean, 'no', false],
            'bool 6' => [ColumnType::Boolean, 'FALSCH', false],
            'bool 7' => [ColumnType::Boolean, 'truE', true],
            'bool 8' => [ColumnType::Boolean, 'yES', true],
            'bool 9' => [ColumnType::Boolean, 'WAHR', true],
            'bool 10' => [ColumnType::Boolean, [], false],
            'bool 11' => [ColumnType::Boolean, [1], true],
            'bool 12' => [ColumnType::Boolean, 0, false],
            'bool 13' => [ColumnType::Boolean, -1, true],
            'bool 14' => [ColumnType::Boolean, 1, true],

        ];
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFormat(ColumnType $type, mixed $input, mixed $output): void
    {

        $this->assertSame($output, $type->format($input));
    }
}
