<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Elements;

use Closure;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;

class ColumnSchemaTest extends TestCase
{
    public function testFormat(): void
    {
        $columnSchema = new ColumnSchema(ColumnType::Boolean);
        $this->assertSame(true, $columnSchema->format('TRUE', []));
    }

    public static function dataProviderTransform(): array
    {
        $row1 = [
            'a' => 1,
            'b' => 'Hello',
            'c' => 'World',
        ];
        $concat = function ($param, $row) {
            return $row['b'].' '.$row['c'];
        };

        return [
            //  name            $param   $closure  $row     $result
            'null closure 1' => [123, null, [], 123],
            'null closure 2' => ['abc', null, [], 'abc'],
            'null closure 3' => [null, null, [], null],
            'string function 1' => ['abc', fn ($x) => strtoupper($x), [], 'ABC'],
            'string function 2' => [null, $concat, $row1, 'Hello World'],
        ];
    }

    /**
     * @dataProvider dataProviderTransform
     *
     * @return void
     */
    public function testTransform(mixed $param, ?Closure $closure, array $row, mixed $result)
    {
        $columnSchema = new ColumnSchema(ColumnType::Closure, $closure);
        $this->assertSame($result, $columnSchema->transform($param, $row));
    }
}
