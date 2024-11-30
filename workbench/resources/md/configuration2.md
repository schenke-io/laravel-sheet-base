
For the two possible ID columns `addId` and `addDot` the following applies:
* must be only one ID column
* ID column must be the first column
* empty ID values are skipped
* if ID value repeats, its overwrite existing data - this allows to read from one file and add from another

Here an example of the addClosure method:
```php
class MySchema extends SheetBaseSchema
{
    public function define(): void
    {
        $concat = function ($key, $row) {
            return ($row[$key] ?? '?').' '.$row['c'];
        };
        $this->addId();
        $this->addString('name');
        $this->addClosure('name2', $concat);
    }
}
```
