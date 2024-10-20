# Laravel Sheet Base

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)
![Coverage](https://raw.githubusercontent.com/schenke-io/laravel-sheet-base/main/tests/coverage/coverage.svg)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-sheet-base/run-tests.yml?branch=main&label=run-tests&style=flat-square)](https://github.com/schenke-io/laravel-sheet-base/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)


____include(warning)

The **Laravel Sheet Base** package simplifies data processing by offering a collection of classes specifically tailored for building efficient data conversion pipelines. These pipelines are well-suited for scenarios where data modifications occur infrequently.

Here are some ideal use cases for this package:

* **Manually edited data sources:** When most of your data originates from manual edits by specialists.
* **Infrequent data changes:** When your data remains relatively stable over time.
* **Command line efficiency:** When utilizing a developer console command proves more efficient than a web form for data processing tasks.

These pipelines involve **reading data** from **endpoints**
on one end and **processing & storing** it using a **writer**
on the other. Each pipeline uses a **schema** to define
the **table format** for the writer.

The Laravel Sheet Base package simplifies managing and 
transforming data in your Laravel applications. 
It offers several useful features:

- **Work with various data formats:** Import data from 
CSV files and export it to JSON, YAML, or Neon formats.
- **Extract translations:** Easily gather translations 
from different sources and generate language files.
- **Combine data sources:** Merge data from multiple 
sources into a single, targeted output.
- **Transform and write data:** Read data, perform 
calculations, and write the resulting transformed data.
- **Generate files:** Create files for seeding, backups, 
configuration, or Laravel Sushi integration.

The package utilizes a flexible and extensible pipeline 
architecture, making data management tasks efficient and straightforward.



<!-- TOC -->
* [Laravel Sheet Base](#laravel-sheet-base)
  * [Supported Readers](#supported-readers)
  * [Supported Writers](#supported-writers)
  * [Schema building blocks](#schema-building-blocks)
  * [Pipeline Pumping Process](#pipeline-pumping-process)
  * [Installation](#installation)
  * [Configuration](#configuration)
    * [Filesystem](#filesystem)
    * [Pipelines](#pipelines)
    * [Schema](#schema)
    * [Schema columns](#schema-columns)
    * [Endpoints](#endpoints)
      * [Accessing Files on Disks: Defining Endpoints](#accessing-files-on-disks-defining-endpoints)
      * [Endpoints as filenames](#endpoints-as-filenames)
        * [Extending Existing Classes](#extending-existing-classes)
        * [Using Filename Extensions](#using-filename-extensions)
      * [Endpoints as array](#endpoints-as-array)
      * [Endpoint to write language files](#endpoint-to-write-language-files-)
      * [Endpoint to read Google Sheets](#endpoint-to-read-google-sheets)
    * [Verify the configuration](#verify-the-configuration)
  * [Pumping](#pumping)
  * [License](#license)
<!-- TOC -->


## Supported Readers

- File readers
- Array readers
- Collection readers
- Eloquent model readers
- Cloud file readers
- Google Sheets readers


## Supported Writers

The package includes versatile writers capable of handling various file formats:

- typical data file formats (JSON, Neon, YAML)
- PHP (config) files (suitable for tests, configuration files, or Laravel Sushi)
- Laravel language php-files

Additionally, all written files that support comments include 
remarks about the reference file, discouraging direct 
editing.

## Schema building blocks

Here are the three basic building blocks for creating a schema:
+ **ID-based tables:** These tables have many columns and a single ID field in the first column.
+ **Language files:** These files use a key in dot notation and have separate columns for each language code.
+ **Simple sources:** These are single-column sources that lack a key column, the ID is generated automatically.


## Pipeline Pumping Process

Here's the breakdown of how a pipeline pumps data:

+ **Read data:** Each line of data is read into a "dataRow".
+ **Assign key:** The dataRow gets its key either from an "ID" column or a numeric sequence.
+ **New key:** If the key is new, a new "target dataRow" is created for that ID, with all its columns filled with null values.
+ **Fill data:** For each key required by the schema, its value is searched for in the source data. If found, it's added to the corresponding column of the target dataRow.
+ **Repeat:** This process repeats for each source dataRow, even if the same ID appears multiple times.
+ **Write output:** Once all sources are read, the entire dataset is written from memory to a target, using the appropriate format function for each column.

**Pipeline execution:**
+ Pipelines are executed in the alphabetic order of the pipeline names.
+ Important points:
  + **No warning for duplicate IDs:** Sources can have duplicate IDs, but the final result only allows unique IDs.
  + **Optional data in sources:** Sources might not have all the data defined in the Schema.
  + **Pipelines can be chained:** One pipeline's output can be another's input.


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
Configure the default disk in `config/filesystems.php`. 
Define a new disk `sheet-base` and specify its location. 
This will become the default place for files to be read 
and write.

### Pipelines
Configure the pipelines in `config/sheet-base.php`.
Under the primary key `pipelines` add names for each pipeline
and define these 3 keys:
 
| key     | purpose                           | type                                     |
|---------|-----------------------------------|------------------------------------------|
| sources | where does the data comes from    | array of strings of classes or filenames |
| schema  | schema of columns in output table | class name                               |
| target  | where does the data goes to       | string of class or filename              |

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


____include(table_columns)



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

### Endpoints
A pipeline defines data flow between two designated points, 
called **endpoints**. 
You can define these endpoints in many ways.

#### Accessing Files on Disks: Defining Endpoints
* **Extending Existing Classes:** This approach leverages existing class structures with additional attributes for specific file types.
* **Using Filename Extensions:** This method utilizes unique file extensions to identify and interact with different file formats.

Use filenames when you just want to read and write to the default `sheet-base` disk.
Use classes when you want to change the disk or want


#### Endpoints as filenames
In the following cases, endpoints can be defined in two ways:

##### Extending Existing Classes
Create a class that inherits from one of the provided endpoint classes.
Each extended class must define in `$path` a path to the file.
The used disk can be overwritten in  `$disk` as well.

##### Using Filename Extensions
Define a file with a specific extension,
associated with the desired endpoint behavior.
The file is located at the  `sheet-base` $disk.




____include(table_endpoints)




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
        return [
            1 => ['a' => 1, 'b' => 2],
            2 => ['a' => 4, 'b' => 5],
        ];
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
    // were to write the language files
    public string $root = '/'; 
    // which first parts of the dot-keys should result in files
    public array $fileBases = ['home']; 
}


````
#### Endpoint to read Google Sheets

First get a service account in the Google api console and download the json file. 
Then add the key `GOOGLE_APPLICATION_CREDENTIALS` to the `.env` file and fill the
path to this file. 
````bash
# .env
GOOGLE_APPLICATION_CREDENTIALS=directory/google/file.json
````
Create an empty Google sheets document and share it with the email 
from the service account.<br>

There are two ways to configure the spreadsheet ID used by this plugin:
- directly in your EndpointClass 
- in the config file (recommended for clarity and multiple files)

In both cases you fill the worksheet name in `$sheetName` and 
get than the URL of the Google sheet.
The spreadsheet ID is the part of the URL after `/d/` and 
before `/edit`. For example, in the 
URL `https://docs.google.com/spreadsheets/d/123ABC-xyz123/edit`, the spreadsheet ID is 123ABC-xyz123.

In `$spreadsheetId` you enter either the ID itself or the name of the 
key in `config/sheet-base.php` as shown below.

````php
class GoogleSheetLang extends EndpointReadGoogleSheet
{
    public string $spreadsheetId = '1ttfjdfdjfdfjdfdjfdfdkfdfdQkGDE';
    # or: public string $spreadsheetId = 'File-Main';
    public string $sheetName = 'Sheet1';
}

# in config/sheet-base.php 
    'pipelines' => [
    .....
    ],
    'spreadsheets' => [
        'File-Main' => '1ttfjdfdjfdfjdfdjfdfdkfdfdQkGDE'
    ]        
  
  

````
The first row in the spreadsheet must contain the column names, as specified in the pipeline schema. The table width is determined by the number of columns with header values. Additional headers to the right are ignored. Data reading stops when the first column is empty, and subsequent rows are discarded.
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
