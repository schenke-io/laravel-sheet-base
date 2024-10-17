<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Elements;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\ColumnSchema;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;

class ColumnSchemaTest extends TestCase
{
    public function testFormat(): void
    {
        $columnSchema = new ColumnSchema(ColumnType::Boolean);
        $this->assertSame(true, $columnSchema->format('a', ['a' => true]));
    }

    public static function dataProviderTransform(): array
    {
        $row1 = [
            'a' => 1,
            'b' => 'Hello',
            'c' => 'World',
        ];
        $concat = function ($key, $row) {
            return ($row[$key] ?? '?').' '.$row['c'];
        };

        return [
            //  name            $key   $closure  $row     $result
            'null closure 1' => ['a', null, ['a' => 123], 123],
            'null closure 2' => ['a', null, ['a' => 'abc'], 'abc'],
            'null closure 3' => ['a', null, ['a' => null], null],
            'string function 1' => ['a', fn ($x, $row) => strtoupper($row[$x] ?? ''), ['a' => 'abc'], 'ABC'],
            'string function 2' => ['b', $concat, $row1, 'Hello World'],
            'string function 3' => ['x', $concat, $row1, '? World'],
        ];
    }

    #[DataProvider('dataProviderTransform')]
    /**
     * @return void
     */
    public function testTransform(string $key, ?Closure $closure, array $row, mixed $result)
    {
        $columnSchema = new ColumnSchema(ColumnType::Closure, $closure);
        $this->assertSame($result, $columnSchema->format($key, $row));
    }
}
