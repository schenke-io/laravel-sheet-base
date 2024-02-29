<?php

namespace SchenkeIo\LaravelSheetBase\Endpoints\Writers;

use Illuminate\Support\Arr;
use SchenkeIo\LaravelSheetBase\Elements\PipelineData;
use SchenkeIo\LaravelSheetBase\EndpointBases\StorageTreeWriter;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;
use SchenkeIo\LaravelSheetBase\Skills\MakePhp;

class EndpointWriteLang extends StorageTreeWriter
{
    use MakePhp;

    protected function getRoot(): string
    {
        // windows compatible
        return str_replace('\\', '/', substr(lang_path(), strlen(base_path())));
    }

    /**
     * @throws EndpointCodeException
     */
    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        /*
         * we have to move the column/language from the end to the front
         */
        $translation = [];
        foreach (Arr::dot($pipelineData->toArray()) as $key => $value) {
            $value = $value == '' ? null : $value;
            $parts = explode('.', $key);
            if (count($parts) < 3) {
                throw new EndpointCodeException(class_basename($this), "the given key would result in invalid translation files: $key");
            }
            // Get the last item from the array
            $lastItem = array_pop($parts);
            // Move the last item to the first position
            array_unshift($parts, $lastItem);
            data_set($translation, implode('.', $parts), $value);
        }

        foreach ($translation as $language => $files) {
            foreach ($files as $fileBase => $fileContent) {
                $path = $this->getRoot()."/$language/$fileBase.php";
                $this->storagePut($path, $this->getPhp($fileContent, $writingClass));
            }
        }
    }
}
