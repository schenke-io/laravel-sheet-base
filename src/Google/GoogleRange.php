<?php

namespace SchenkeIo\LaravelSheetBase\Google;

use SchenkeIo\LaravelSheetBase\Exceptions\GoogleSheetException;

readonly class GoogleRange
{
    /**
     * @throws GoogleSheetException
     */
    public function __construct(
        protected int $sheetIndex,
        protected string $sheetName,
        protected int $rowIndex,
        protected int $columnIndex,
        protected int $width = 1,
        protected int $height = 1
    ) {
        $className = class_basename($this);
        if ($this->sheetIndex < 0) {
            throw new GoogleSheetException($className, 'sheet index must be >= 0');
        }
        if (strlen($this->sheetName) < 2) {
            throw new GoogleSheetException($className, 'sheet name length is < 2');
        }
        if ($this->columnIndex < 0) {
            throw new GoogleSheetException($className, 'column < 0');
        }
        if ($this->rowIndex < 0) {
            throw new GoogleSheetException($className, 'row < 0');
        }
        if ($this->columnIndex > 25) {
            throw new GoogleSheetException($className, 'column > 25');
        }
        if ($this->width < 1) {
            throw new GoogleSheetException($className, 'width < 1');
        }
        if ($this->height < 1) {
            throw new GoogleSheetException($className, 'height < 1');
        }
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
