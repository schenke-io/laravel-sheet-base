
## Configuration

### Filesystem
Configure the default disk in `config/filesystems.php`.
Define a new disk `sheet-base` and specify its location.
This will become the default place for files to be read
and write.

### Pipelines
Configure the pipelines in `config/sheet-base.php`.
Under the primary key `pipelines` add names for each pipeline
and define these 3 keys:

| key     | purpose                                             | type                                     |
|---------|-----------------------------------------------------|------------------------------------------|
| sources | where does the data comes from                      | array of strings of classes or filenames |
| schema  | schema of columns in output table                   | class name                               |
| target  | where does the data goes to                         | string of class or filename              |
| filter  | list of ids which the pipeline should allow to pass | string of class or filename              |
| sync    | syncs data back to Google Sheets                    | boolean true / false                     |

### Schema
Each pipeline has a Schema, which defines the columns of the written file.
Create a schema by:

+ **Inheriting:** Extend the `SheetBaseSchema` class.
+ **Defining columns:** Override the `define()` method.
+ **Adding columns:** Use one of the `$this->add*` methods within
  `define()` to list columns with unique names.


Here a simple example of a Schema class:
```php

use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

class MySchema extends SheetBaseSchema
{
    public function define(): void
    {
        $this->addId();
        $this->addString('name');
    }
}
```

### Schema columns
The following columns are possible:

- `addId(string $name = 'id')`: Standard ID column.
- `addDot(string $name = 'id')`: Dot notation ID column (for language files).
- `addString(string $name)`: Standard string column.
- `addNullString(string $name)`: String column that can be null.
- `addUnsigned(string $name)`: Unsigned integer column.
- `addUnsignedNotNull(string $name)`: Unsigned integer column that cannot be null.
- `addFloat(string $name)`: Floating point number column.
- `addBool(string $name)`: Boolean column.
- `addDateTime(string $name)`: DateTime column.
- `addLanguage(string $name)`: Language column (2-char name).
- `addClosure(string $name, Closure $closure)`: Column calculated by a closure.
