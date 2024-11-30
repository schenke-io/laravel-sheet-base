<?php

namespace SchenkeIo\LaravelSheetBase\Helpers;

class Chunks
{
    /**
     * splits the size of keys in good batches
     */
    public static function batchCount(int $keyCount): int
    {
        return max(1, floor(log($keyCount, 2.5)));
    }

    /**
     * @param  array<int,mixed>  $data
     */
    public static function batchSize(array $data): int
    {
        $dataCount = count($data);

        return (int) ceil($dataCount / self::batchCount($dataCount));
    }

    /**
     * @param  array<int,mixed>  $data
     */
    public static function splitIntoBatches(array $data): array
    {
        $pointer = 0;
        $batchSize = self::batchSize($data);
        $dataCount = count($data);

        $batches = [];
        do {
            $batches[] = array_slice($data, $pointer, $batchSize);
            $pointer += $batchSize;
        } while ($pointer < $dataCount);

        return $batches;
    }
}
