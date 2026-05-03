<?php

namespace SchenkeIo\LaravelSheetBase\Skills;

trait MakePhp
{
    use Comments;

    /**
     * @param  array<mixed, mixed>  $data
     */
    protected function getPhp(array $data, string $writer): string
    {

        $comment = $this->getComment('//', $writer);
        $pretty = $this->prettyArray($data);

        return <<<PHP
<?php

$comment

return $pretty;

PHP;

    }

    /**
     * @param  array<mixed, mixed>  $data
     */
    protected function prettyArray(array $data, int $indent = 0): string
    {
        $result = '['.PHP_EOL;
        $indentStr = str_repeat('    ', $indent + 1);
        $nextIndent = $indent + 1;

        foreach ($data as $key => $value) {
            $result .= $indentStr;
            if (is_string($key)) {
                $result .= "'".addslashes($key)."' => ";
            }

            if (is_array($value)) {
                $result .= $this->prettyArray($value, $nextIndent);
            } elseif (is_string($value)) {
                $result .= "'".addslashes($value)."'";
            } elseif (is_bool($value)) {
                $result .= $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $result .= 'null';
            } elseif (is_int($value) || is_float($value)) {
                $result .= $value;
            } else {
                $result .= 'null';
            }

            $result .= ','.PHP_EOL;
        }

        $result .= str_repeat('    ', $indent).']';

        return $result;
    }
}
