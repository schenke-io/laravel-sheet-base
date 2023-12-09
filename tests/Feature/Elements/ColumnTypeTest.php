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

        ];
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFormat(ColumnType $type, mixed $input, mixed $output): void
    {

        $this->assertEquals($output, $type->format($input));
    }
}
