# Sheet Base Configuration

This skill explains how to configure the Laravel Sheet Base package.

## 1. Config File: `config/sheet-base.php`

The package uses a central configuration file to define pipelines and spreadsheet mappings.

### Pipelines
Pipelines define the data flow from sources to a target using a schema.

<code-snippet name="Pipeline Configuration" lang="php">
'pipelines' => [
    'my_pipeline' => [
        'sources' => [
            'data.csv',              // File-based source
            MySource::class,         // Class-based source
        ],
        'schema'  => MySchema::class, // Schema defining columns
        'target'  => 'output.json',   // Target file or class
        'filter'  => MyFilter::class, // Optional: provides the list of IDs to keep
        'sync'    => true,            // Optional: marks filtered-out rows red in the Google Sheet
    ],
],
</code-snippet>

### Spreadsheets
Mapping shorthand names to Google Spreadsheet IDs.

<code-snippet name="Spreadsheet Mapping" lang="php">
'spreadsheets' => [
    'File-Main' => '1ttfjdfdjfdfjdfdjfdfdkfdfdQkGDE',
],
</code-snippet>

## 2. Filesystem: `config/filesystems.php`

The package expects a disk named `sheet-base` to be defined. This is the default location for reading and writing files.

<code-snippet name="Filesystem Setup" lang="php">
'disks' => [
    'sheet-base' => [
        'driver' => 'local',
        'root'   => storage_path('app/sheet-base'),
    ],
],
</code-snippet>

## 3. Google API Credentials

To use Google Sheets, set the `GOOGLE_APPLICATION_CREDENTIALS` environment variable in your `.env` file to the path of your service account JSON file.

<code-snippet name="Environment Variable" lang="bash">
GOOGLE_APPLICATION_CREDENTIALS=storage/app/google-credentials.json
</code-snippet>

## Pitfalls
- **Missing `sheet-base` disk:** Ensure the disk is defined in `config/filesystems.php`.
- **Invalid JSON credentials path:** The path in `GOOGLE_APPLICATION_CREDENTIALS` must be valid and accessible.
- **`sync: true` constraints:** Sync only works on language pipelines (schema uses `addDot` + target is `EndpointWriteLang`). It also requires exactly one source (a `EndpointReadGoogleSheet` subclass) and a `filter`. Using `sync: true` on a non-language pipeline or without a filter throws a `ConfigErrorException`.
- **Each target can only be used once:** Two pipelines cannot share the same target path or class. A duplicate target throws a `ConfigErrorException`.
- **Maximum one language pipeline:** Only one pipeline with a language schema (`addDot` + `EndpointWriteLang`) is allowed per config.
