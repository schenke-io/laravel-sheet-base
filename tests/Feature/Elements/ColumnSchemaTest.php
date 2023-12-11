<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use Closure;
use Orchestra\Testbench\TestCase;
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
        return [
            #  name            $param   $closure  $row     $result
            'null closure 1' => [123, null, [], 123],
            'null closure 2' => ['abc', null, [], 'abc'],
            'null closure 3' => [null, null, [], null],
        ];
    }

    /**
     * @dataProvider dataProviderTransform
     * @param mixed $param
     * @param \Closure $closure
     * @param array $row
     * @param mixed $result
     * @return void
     */
    public function testTransform(mixed $param, ?Closure $closure, array $row, mixed $result)
    {
        $columnSchema = new ColumnSchema(ColumnType::Closure, $closure);
        $this->assertSame($result, $columnSchema->transform($param, $row));
    }
}
