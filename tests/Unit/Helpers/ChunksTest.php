<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Helpers\Chunks;

class ChunksTest extends TestCase
{
    public static function dataProviderBatches(): array
    {
        return [
            'set 1' => [
                range(1, 3), [
                    range(1, 3),
                ],
            ],
            'set 2' => [
                range(1, 20), [
                    range(1, 7),
                    range(8, 14),
                    range(15, 20),
                ],
            ],
        ];
    }

    #[DataProvider('dataProviderBatches')]
    public function test_split_into_batches(array $data, array $batches): void
    {
        $this->assertEquals($batches, Chunks::splitIntoBatches($data));
    }

    public static function dataProviderBatchSizes(): array
    {
        return [
            'set 1' => [1, 1],
            'set 2' => [10, 5],
            'set 3' => [100, 20],
        ];
    }

    #[DataProvider('dataProviderBatchSizes')]
    public function test_can_set_sizes(int $keySize, int $batchSize): void
    {
        $keys = range(1, $keySize);
        $this->assertEquals(Chunks::batchSize($keys), $batchSize);
    }

    public static function dataProviderKeyCounts(): array
    {
        return [
            'small 1' => [1, 1, 1],
            'small 2' => [2, 1, 1],
            'small 3' => [5, 1, 2],
            'small 4' => [7, 1, 2],

            'medium 1' => [10, 2, 2],
            'medium 2' => [20, 2, 3],
            'medium 3' => [30, 2, 3],

            'large 1' => [100, 4, 7],
            'large 2' => [200, 5, 8],
            'large 3' => [300, 6, 9],
        ];
    }

    #[DataProvider('dataProviderKeyCounts')]
    public function test_batch_count(int $keyCount, int $minBatchCount, int $maxBatchCount): void
    {
        $batchCount = Chunks::batchCount($keyCount);
        $this->assertTrue($batchCount >= $minBatchCount && $batchCount <= $maxBatchCount, "from $keyCount to $batchCount ($minBatchCount, $maxBatchCount)");
    }
}
