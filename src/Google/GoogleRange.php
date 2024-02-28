<?php

namespace SchenkeIo\LaravelSheetBase\Google;

class GoogleRange
{
    /**
     * @throws \Throwable
     */
    public function __construct(
        protected readonly int $sheetIndex,
        protected readonly string $sheetName,
        protected readonly int $rowIndex,
        protected readonly int $columnIndex,
        protected readonly int $width = 1,
        protected readonly int $height = 1
    ) {
        throw_if($this->sheetIndex < 0, 'sheet index must be >= 0');
        throw_if(strlen($this->sheetName) < 2, 'sheet name length is < 2');
        throw_if($this->columnIndex < 0, 'column < 0');
        throw_if($this->rowIndex < 0, 'row < 0');
        throw_if($this->columnIndex > 25, 'colun > 25');
        throw_if($this->width < 1, 'width < 1');
        throw_if($this->height < 1, 'height < 1');
    }

    public function asRange(): array
    {
        return [
            'sheetId' => $this->sheetIndex,
            'startRowIndex' => $this->rowIndex,
            'endRowIndex' => $this->rowIndex + $this->height,
            'startColumnIndex' => $this->columnIndex,
            'endColumnIndex' => $this->columnIndex + $this->width,
        ];
    }

    public function asString(): string
    {
        $name = $this->sheetName;
        $name = str_contains($name, ' ') ? "'$name'" : $name;

        return $name.
            '!'.
            chr(ord('A') + $this->columnIndex).
            ($this->rowIndex + 1).
            ':'.
            chr(ord('A') + $this->columnIndex + $this->width - 1).
            ($this->rowIndex + 1 + $this->height - 1);

    }
}
