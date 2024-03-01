<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use ReflectionMethod;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
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
        $methodNames = array_map(function (ReflectionMethod $reflectionMethod) {
            return $reflectionMethod->name;
        }, $reflection->getMethods());
        $methodName = 'add'.$columnType->value;
        $this->assertTrue(in_array($methodName, $methodNames), 'assert '.SheetBaseSchema::class." has method $methodName");
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
            'unsigned decimal negativ' => [ColumnType::Unsigned, -3, 0],
            'unsigned string 1' => [ColumnType::Unsigned, '3', 3],
            'unsigned string 2' => [ColumnType::Unsigned, '  3', 3],
            'unsigned string 3' => [ColumnType::Unsigned, '  3    ', 3],
            'unsigned string 4' => [ColumnType::Unsigned, '  3  Test  ', 3],
            'unsigned string 5' => [ColumnType::Unsigned, '  3   5  Test  ', 3],
            'unsigned string 6' => [ColumnType::Unsigned, ' a b c ', 0],
            'unsigned string 7' => [ColumnType::Unsigned, '', null],
            'unsigned not null 1' => [ColumnType::UnsignedNotNull, '', 0],
            'unsigned not null 2' => [ColumnType::UnsignedNotNull, null, 0],

            'float 1' => [ColumnType::Float, '', null],
            'float 2' => [ColumnType::Float, null, null],
            'float 3' => [ColumnType::Float, ' 0.123 ', 0.123],
            'float 4' => [ColumnType::Float, '1,23', 1.23],
            'float 5' => [ColumnType::Float, ' 1,23 Euro ', 1.23],
            'float 6' => [ColumnType::Float, 0.123, 0.123],
            'float 7' => [ColumnType::Float, ' 1.234,56', 1234.56],
            'float 8' => [ColumnType::Float, ' 1,234.56', 1234.56],
            'float 9' => [ColumnType::Float, '  1234 ', 1234.0],

            'bool 1' => [ColumnType::Boolean, null, false],
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

            'string 1' => [ColumnType::String, '', ''],
            'string 2' => [ColumnType::String, null, ''],
            'string 3' => [ColumnType::String, 12, '12'],

            'null string 1' => [ColumnType::NullString, '', null],
            'null string 2' => [ColumnType::NullString, null, null],
            'null string 3' => [ColumnType::NullString, ' ', ' '],
            'null string 4' => [ColumnType::NullString, 1234, '1234'],

            'language 1' => [ColumnType::Language, '', null],
            'language 2' => [ColumnType::Language, null, null],
            'language 3' => [ColumnType::Language, ' ', ' '],
            'language 4' => [ColumnType::Language, 1234, '1234'],

            'data-time 1' => [ColumnType::DateTime, null, null],
            'data-time 2' => [ColumnType::DateTime, '', null],
            'data-time 3' => [ColumnType::DateTime, '2010-12-31', '2010-12-31 00:00:00'],
            'data-time 4' => [ColumnType::DateTime, 'midnight first day of november 2012', '2012-11-01 00:00:00'],
            'data-time 5' => [ColumnType::DateTime, '12:00 Uhr 1.12.2007', null],
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
