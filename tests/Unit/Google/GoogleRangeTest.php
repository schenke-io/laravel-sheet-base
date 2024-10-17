<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Google;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Google\GoogleRange;

class GoogleRangeTest extends TestCase
{
    public static function dataProviderRanges(): array
    {
        return [
            'standard 1 cell' => [0, 'Sheet1', 0, 0, 1, 1, 'Sheet1!A1:A1'],
            'long name 1 cell' => [0, 'Sheet 1', 0, 0, 1, 1, "'Sheet 1'!A1:A1"],
            'wrong index' => [-1, '', 0, 0, 0, 0, null],
            'wrong name' => [0, '', 0, 0, 0, 0, null],
            'col to small' => [0, 'sheet1', 0, -1, 1, 1, null],
            'col to big' => [0, 'sheet1', 0, 30, 1, 1, null],
            'row to small' => [0, 'sheet1', -1, 0, 0, 1, null],
            'wrong width' => [0, 'sheet1', 0, 0, 0, 1, null],
            'wrong height' => [0, 'sheet1', 0, 0, 1, 0, null],
        ];
    }

    #[DataProvider('dataProviderRanges')]
    public function testRangeToString(int $index, string $name, int $row, int $col, int $width, int $height, ?string $range): void
    {
        if (is_null($range)) {
            $this->expectException(\RuntimeException::class);
        }
        $this->assertEquals($range, (new GoogleRange($index, $name, $row, $col, $width, $height))->asString());
    }

    public function testReturnAsRange()
    {
        $return = [
            'sheetId' => '1',
            'startRowIndex' => 0,
            'endRowIndex' => 3,
            'startColumnIndex' => 0,
            'endColumnIndex' => 2,
        ];
        $this->assertEquals($return, (new GoogleRange(1, 'abcde', 0, 0, 2, 3))->asRange());
    }
}
