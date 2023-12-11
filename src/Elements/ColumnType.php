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

    case Boolean = 'Bool';

    case Closure = 'Closure';

    public function isId(): bool
    {
        return match ($this) {
            self::ID, self::Dot => true,
            default => false
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
            self::Boolean => $this->formatBoolean($param),
            default => $param
        };
    }

    private function formatBoolean(mixed $param): ?bool
    {
        if (is_null($param)) {
            return null;
        } elseif (is_numeric($param)) {
            return $param !== 0;
        } elseif (is_string($param)) {
            return ! preg_match('@^(false|no|falsch)$@i', $param);
        } else {
            return (bool) $param;
        }
    }

    private function formatFloat(mixed $param): ?float
    {
        if (is_numeric($param)) {
            return (float) $param;
        } elseif (is_string($param) && strlen($param) > 0) {
            $posKomma = strpos($param, ',');
            $posDot = strpos($param, '.');
            if ($posDot && $posKomma) {
                if ($posDot > $posKomma) {
                    // english
                    return (float) str_replace(',', '', $param);
                } else {
                    // german
                    return (float) str_replace(['.', ','], ['', '.'], $param);
                }
            } elseif ($posKomma) {
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
