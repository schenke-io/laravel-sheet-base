<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

enum ColumnType: string
{
    case ID = 'Id';
    case Dot = 'Dot';
    case String = 'String';
    case Unsigned = 'Unsigned';
    case Language = 'Language';

    case Float = 'Float';

    public function isId(): bool
    {
        return match ($this) {
            self::ID, self::Dot => true,
            default => false
        };
    }

    public function getName(array $arguments): string
    {
        return match ($this) {
            self::ID, self::Dot => $arguments[0] ?? 'id',
            default => $arguments[0] ?? ''
        };
    }

    public function getPipelineType(): PipelineType
    {
        return $this == ColumnType::Dot ? PipelineType::Tree : PipelineType::Table;
    }

    public function format(mixed $param): mixed
    {
        return match ($this) {
            self::Unsigned => $this->formatUnsigned($param),
            self::Float => $this->formatFloat($param),
            default => $param
        };
    }

    private function formatFloat(mixed $param): ?float
    {
        if (is_numeric($param)) {
            return (float) $param;
        } elseif (is_string($param) && strlen($param) > 0) {
            if (str_contains($param, ',')) {
                if (str_contains($param, '.')) {
                    // remove the dots
                    $param = str_replace('.', '', $param);
                }

                return (float) str_replace(',', '.', $param);
            }
        }

        return null;
    }

    private function formatUnsigned(mixed $param): ?int
    {
        if (is_numeric($param)) {
            return max(0, abs(floor($param)));
        } elseif (is_string($param) && strlen($param) > 0) {
            if (preg_match('@(\d+)@', $param, $matches)) {
                return (int) $matches[1];
            } else {
                return 0;
            }
        }

        return null;
    }
}
