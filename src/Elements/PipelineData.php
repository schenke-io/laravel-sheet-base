<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use Illuminate\Console\Command;
use SchenkeIo\LaravelSheetBase\Contracts\IsReader;
use SchenkeIo\LaravelSheetBase\Exceptions\EndpointCodeException;

/**
 * generic data structure used inside the pipeline
 */
final class PipelineData
{
    /**
     * @var array<string,array<string,array>>
     */
    protected array $data = [];

    protected string $idName = 'id';

    protected PipelineType $pipelineType;

    public function __construct(public SheetBaseSchema $sheetBaseSchema)
    {
        $this->idName = $this->sheetBaseSchema->getIdName();
        $this->pipelineType = $this->sheetBaseSchema->getPipelineType();
    }

    public static function fromArray(array $data, SheetBaseSchema $sheetBaseSchema): PipelineData
    {
        $me = new PipelineData($sheetBaseSchema);
        $me->data = $data;

        return $me;
    }

    public static function fromType(PipelineType $pipelineType): PipelineData
    {
        return new PipelineData($pipelineType->getSchema());
    }

    /**
     * we return the data based on the pipeline type
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return array list of keys which were removed by the filter
     *
     * @throws EndpointCodeException
     */
    public function filterKeysOff(
        Command $cmd,
        string $namePipeline,
        ?IsReader $filterEndpoint = null): array
    {
        if (is_null($filterEndpoint)) {
            return [];
        }

        $pipelineDataFilter = PipelineData::fromType($this->pipelineType);
        $filterEndpoint->fillPipeline($pipelineDataFilter);

        $keysPumped = array_keys($this->toArray());
        $keysFilter = array_keys($pipelineDataFilter->toArray());
        $keysToRemove = array_values(array_diff($keysPumped, $keysFilter));
        $keysMissing = array_diff($keysFilter, $keysPumped);
        /*
         * remove data from pipeline
         */
        foreach ($keysToRemove as $key) {
            unset($this->data[$key]);
        }
        $cmd->info(
            sprintf(
                "pipeline '%s' received %d records, %d passed, filter '%s' filtered %d out, keys missing: %d",
                $namePipeline,
                count($keysPumped),
                count($this->data),
                $filterEndpoint->toString(),
                count($keysToRemove),
                count($keysMissing)
            )
        );

        return $keysToRemove;
    }

    public function addRow(array $row): void
    {

        $id = '';
        if (isset($row[$this->idName])) {
            // remove the id if set
            $id = $row[$this->idName];
            unset($row[$this->idName]);
        }
        if (strlen($id) < 1) {
            // skip empty ID values
            return;
        }
        if ($row == []) {
            // we just had an id field
            $this->data[$id] = [];

            return;
        }
        /*
         * we have a valid id with a data row
         */
        foreach ($this->sheetBaseSchema->getColumns() as $columnName => $columnDefinition) {
            if ($columnName == $this->idName) {
                // we still have the id column defined in the schema
                $this->data[$id] = $this->data[$id] ?? [];
            } elseif (
                isset($row[$columnName]) ||  // we have data to store
                ! isset($this->data[$id][$columnName]) // we have to set empty data
            ) {
                // set or overwrite
                $this->data[$id][$columnName] = $columnDefinition->format($columnName, $row);
            }
        }
    }
}
