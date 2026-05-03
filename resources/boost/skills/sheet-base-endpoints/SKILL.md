# Sheet Base Endpoints

This skill explains how to define endpoints (sources and targets) in your pipelines.

## Endpoint Styles

Endpoints can be defined in three main ways:

### 1. File-based (Auto-mapping)
By using a filename with a recognized extension, the package automatically chooses the correct reader or writer class. These files are located on the `sheet-base` disk.

Supported extensions: `.csv`, `.tsv`, `.psv`, `.php`, `.yaml`, `.yml`, `.json`, `.neon`, `.txt`.

<code-snippet name="File-based Endpoints" lang="php">
'sources' => ['data/input.csv'],
'target'  => 'output/result.json',
</code-snippet>

### 2. Class-based
For more control or for Google Sheets, extend the appropriate endpoint class and set `$path` (or `$spreadsheetId` / `$sheetName` for Google Sheets). Then use the class name in the config.

<code-snippet name="Class-based file endpoint" lang="php">
use SchenkeIo\LaravelSheetBase\Endpoints\Writers\EndpointWriteJson;

class MyJsonOutput extends EndpointWriteJson
{
    public string $path = 'output/result.json';
    // public string $disk = 'my-disk'; // optional override
}
</code-snippet>

<code-snippet name="Class-based Google Sheet source" lang="php">
use SchenkeIo\LaravelSheetBase\Endpoints\Readers\EndpointReadGoogleSheet;

class MyGoogleSheetSource extends EndpointReadGoogleSheet
{
    // Either the real spreadsheet ID or a key from config('sheet-base.spreadsheets')
    public string $spreadsheetId = 'File-Main';
    public string $sheetName     = 'Sheet1';
}
</code-snippet>

<code-snippet name="Using class-based endpoints in config" lang="php">
'sources' => [MyGoogleSheetSource::class],
'target'  => MyJsonOutput::class,
</code-snippet>

## Mixing Sources and Targets
You can mix file-based and class-based sources in a single pipeline.

<code-snippet name="Mixed Sources" lang="php">
'pipelines' => [
    'sync_data' => [
        'sources' => [
            'local_data.csv',         // File-based (auto-mapped)
            MyGoogleSheetSource::class, // Class-based
        ],
        'schema'  => MainSchema::class,
        'target'  => 'final_output.yaml',
    ],
],
</code-snippet>

## Pitfalls
- **Unsupported extensions:** Using an extension not in the auto-mapping list will throw a `MakeEndpointException`. Use a class-based endpoint instead.
- **Using `EndpointReadGoogleSheet` directly:** It cannot be used without extending — `$spreadsheetId` and `$sheetName` must both be set on the subclass.
- **Readers as targets / writers as sources:** Readers implement `IsReader`; writers implement `IsWriter`. They cannot be swapped. Google Sheets has only a reader (`EndpointReadGoogleSheet`); there is no file-write target for Google Sheets.
- **Forgetting the `sheet-base` disk:** Auto-mapped file paths are resolved against the `sheet-base` disk defined in `config/filesystems.php`.
