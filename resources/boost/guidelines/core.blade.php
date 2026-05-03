@verbatim
# Laravel Sheet Base Overview

The **Laravel Sheet Base** package provides a data pipeline system for Laravel: read from one or more sources, validate through a typed schema, and write to a single target. Pipelines are configured in `config/sheet-base.php` and executed via Artisan.

## 1. Pipelines

Each entry under `pipelines` defines a data flow: sources → schema → target.

<code-snippet name="Pipeline Config" lang="php">
// config/sheet-base.php
return [
    'pipelines' => [
        'users' => [
            'sources' => [
                'data/users.csv',           // resolved by file extension
                App\Endpoints\ExtraUsers::class, // or a class
            ],
            'schema'  => App\Schemas\UserSchema::class,
            'target'  => 'output/users.json',
        ],
    ],
];
</code-snippet>

Pipelines run in alphabetical order of their names. One pipeline's output can be another pipeline's input.

## 2. Schemas

Extend `SheetBaseSchema` and implement the `protected define()` method. The ID column must be first and there can only be one.

<code-snippet name="Table Schema" lang="php">
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

class UserSchema extends SheetBaseSchema
{
    protected function define(): void
    {
        $this->addId();               // numeric or string key
        $this->addString('name');
        $this->addUnsigned('age');
        $this->addBool('active');
    }
}
</code-snippet>

<code-snippet name="Language Schema" lang="php">
class TranslationSchema extends SheetBaseSchema
{
    protected function define(): void
    {
        $this->addDot('key');         // dot-notation key, e.g. home.title
        $this->addLanguage('de');     // column name must be a 2-char language code
        $this->addLanguage('en');
    }
}
</code-snippet>

**Schema rules:**
- A schema using `addLanguage` must use `addDot` as its ID, and every non-ID column must be a language column.
- A schema using `addId` cannot mix in language columns.
- Schemas with a single `addId()` or `addDot()` column (no other columns) are valid.

Available column methods: `addId`, `addDot`, `addString`, `addNullString`, `addUnsigned`, `addUnsignedNotNull`, `addFloat`, `addBool`, `addDateTime`, `addLanguage`, `addClosure`.

## 3. Endpoints

File-based endpoints are resolved automatically from the file extension. Class-based endpoints must extend one of the base classes and set `$path`:

<code-snippet name="Custom File Endpoint" lang="php">
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteJson;

class MyJsonOutput extends EndpointWriteJson
{
    public string $path = 'output/result.json';
}
</code-snippet>

Extension-to-class mapping (readers / writers):

| ext  | reader              | writer              |
|------|---------------------|---------------------|
| csv  | EndpointReadCsv     | EndpointWriteCsv    |
| neon | EndpointReadNeon    | EndpointWriteNeon   |
| yaml | EndpointReadYaml    | EndpointWriteYaml   |
| yml  | EndpointReadYml     | EndpointWriteYml    |
| psv  | EndpointReadPsv     | EndpointWritePsv    |
| tsv  | EndpointReadTsv     | EndpointWriteTsv    |
| txt  | EndpointReadTxt     | EndpointWriteTxt    |
| json | —                   | EndpointWriteJson   |
| php  | —                   | EndpointWritePhp    |
| lang | —                   | EndpointWriteLang   |

## 4. Artisan Commands

```bash
php artisan sheet-base:check   # validate configuration
php artisan sheet-base:pump    # run all pipelines
```

## 5. Skills

Detailed guides for specific operations are available as skills:
- `sheet-base-config`: Configuring pipelines, disks, and Google API credentials.
- `sheet-base-schema`: Defining schemas with typed columns.
- `sheet-base-endpoints`: Choosing and customising reader/writer endpoints.
@endverbatim