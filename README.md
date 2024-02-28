# Laravel Sheet Base

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-sheet-base/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-sheet-base/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)



The **Laravel Sheet Base** package provides a set of classes 
designed for creating pipelines that efficiently handle 
data conversion scenarios where data changes infrequently.

## Overview

On one side of these pipelines are endpoints responsible for reading data, while on the other side, there is a writer that processes and stores the results. Each pipeline is equipped with a schema to describe its tabular data format.

### Supported Readers

- File readers
- Array readers
- Collection readers
- Eloquent model readers
- Cloud file readers
- Google Sheets readers


### Supported Writers

The package includes versatile writers capable of handling various file formats:

- typical data file formats (JSON, Neon, YAML)
- PHP files (suitable for tests, configuration files, or Laravel Sushi)
- Laravel Language files

Additionally, all written files that support comments include 
remarks about the reference file, discouraging direct 
editing.

## Typical Use Cases

The Laravel Sheet Base package is well-suited for a range of applications, including:

- Collecting data from CSV files and exporting them to JSON, YAML, or Neon formats.
- Extracting translations from diverse sources and generating language files.
- Creating files for seeding, backup, configuration, or Laravel Sushi integration.

By offering a flexible and extensible pipeline architecture, this package streamlines the process of managing and transforming data in Laravel applications.


## Installation

You can install the package via composer:

```bash
composer require schenke-io/laravel-sheet-base
```

You can install the config file with:

```bash
php artisan sheet-base:install
```

## Configuration

### Filesystem
Configure the filesystem in `config/filesystems.php`. 
Define a disk named `sheet-base` and specify its location. 
This will become the place for all files to be read and write.

### Pipelines
Configure the pipelines in `config/sheet-base.php`.
Under the primary key `pipelines` and names for each pipeline
and define these 3 keys:
 
| key     | purpose                           | type                                     |
|---------|-----------------------------------|------------------------------------------|
| sources | where does the data comes from    | array of strings of classes or filenames |
| schema  | schema of columns in output table | class name                               |
| target  | where does the data goes to       | string of class or filename              |

### Endpoints
Each pipeline's source and target are both endpoints, 
definable in the following ways.

#### Endpoints as filenames
In the following cases, endpoints can be defined in two ways:
+ **Extending Existing Classes**: Create a class that inherits from one of the provided endpoint classes.
+ **Using Filename Extensions**: Define a file with a specific extension, associated with the desired endpoint behavior.
Each extended class must define a path to the file:


| extension | format                     | reader           | writer            |
|-----------|----------------------------|------------------|-------------------|
| neon      | Nette Object Notation      | EndpointReadNeon | EndpointWriteNeon |
| psv       | pipe seperated values      | EndpointReadPsv  |                   |
| json      | JavaScript Object Notation | EndpointReadJson | EndpointWriteJson |             
| php       | PHP config file            |                  | EndpointWritePhp  |             

````php
# config/sheet-base.php
return [
    'pipelines' => [
        'sources' => [
            'directory/data.neon',
            MyEndpoints\MyData:class
        ],
        .....
    ]
];

````
and the class for it:
````php
# App\MyEndPoints\MyData
class MyData  extends EndpointWriteNeon
{
    public string $path = 'directory/data2.neon';
}
````

#### Endpoints as array
The array endpoints, `EndpointReadArray` and `EndpointWriteArray`, allow for programmatic access to other data, such as Eloquent models, APIs, or special data formats.
````php
# App\MyEndpoints\PhpRead
class PhpRead extends EndpointReadArray{
    public function getArray(): array
    {
        // do magic
        return [1,2,3];
    }
}

# App\MyEndpoints\PhpWrite
class PhpWrite extends EndpointWriteArray{
    public function releasePipeline(PipelineData $pipelineData, string $writingClass): void
    {
        $this->arrayData = $pipelineData->toArray();
        # do magic with $this->arrayData
    }
}
````

#### Endpoint to write language files 

One pipeline can be used to write language php files. 
You need a special schema  and target file. 
````php
# App\MyEndpoints\LanguageSchema
class LanguageSchema extends SheetBaseSchema 
{
    protected function define(): void
    {
        $this->addDot('key');  // must be first, name can be different 
        $this->addLanguage('de');  // only language codes as column names
        $this->addLanguage('en');
    }
}

# App\MyEndpoints\LanguageTarget
class LanguageTarget extends EndpointWriteLang
{
    public string $root = '/'; // were to write the language files
    public array $fileBases = ['home']; // which first parts of the dot-keys should result in files to be written
}


````
#### Endpoint to read Google Sheets

First get a service account in the Google api cosnole and download the json file. Then add 
the key `GOOGLE_APPLICATION_CREDENTIALS` to the `.env` file and fill the
pat to this file. 
````bash
# .env
GOOGLE_APPLICATION_CREDENTIALS=directory/google/file.json
````
Create an empty Google sheets document and share it with the email 
from the service account.<br>
Extract from the url the `$spreadsheetId` and get the name of one sheet. 
Enter both in the class:
````php
class GoogleSheetLang extends EndpointReadGoogleSheet
{
    public string $spreadsheetId = '1ttfjdfdjfdfjdfdjfdfdkfdfdQkGDE';
    public string $sheetName = 'Sheet1';
}

````
The first row must include the column names as defined in the schema of the pipeline.

### Verify the configuration

You can check the configuration after any edit with:

```bash
php artisan sheet-base:check
```

## Pumping

You can pump data through the pipelines with this command:

```bash
php artisan sheet-base:pump
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
