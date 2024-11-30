
#### Endpoints as array

The array endpoints, `EndpointReadArray` and `EndpointWriteArray`, allow for programmatic access to other data, such as Eloquent models, APIs, or special data formats.
````php
// App\MyEndpoints\PhpRead
class PhpRead extends EndpointReadArray{
    public function getArray(): array
    {
        // do magic
        return [
            1 => ['a' => 1, 'b' => 2],
            2 => ['a' => 4, 'b' => 5],
        ];
    }
}

// App\MyEndpoints\PhpWrite
class PhpWrite extends EndpointWriteArray{
    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $this->arrayData = $pipelineData->toArray();
        # do magic with $this->arrayData
    }
}
````
