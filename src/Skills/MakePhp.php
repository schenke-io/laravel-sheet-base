<?php

namespace SchenkeIo\LaravelSheetBase\Skills;

trait MakePhp
{
    use Comments;

    /**
     * @param  array[]|array[][]  $data
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
     * @param  array[]|array[][]  $data
     */
    protected function prettyArray(array $data): string
    {

        $export = var_export($data, true);
        $export = preg_replace('/^([ ]*)(.*)/m', '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);

        return implode(PHP_EOL, array_filter(['['] + $array));

    }
}
