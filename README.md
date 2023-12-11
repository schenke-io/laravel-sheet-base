# Laravel Sheet Base

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-sheet-base/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-sheet-base/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)



The **Laravel Sheet Base** package provides a set of classes designed for creating pipelines that efficiently handle scenarios where data changes infrequently.

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

- JSON
- Neon
- YAML
- PHP files (suitable for tests, configuration files, or Laravel Sushi)
- Laravel Language files

Additionally, all written files that support comments include remarks about the reference file, discouraging direct editing for clarity.

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

You can check the configuration with:

```bash
php artisan sheet-base:check
```

## Pumping

You can pump data through the pipelines with:

```bash
php artisan sheet-base:pump
```

## Testing

```bash
composer test
```

[//]: # ()

[//]: # (## Changelog)

[//]: # ()

[//]: # (Please see [CHANGELOG]&#40;CHANGELOG.md&#41; for more information on what has changed recently.)

[//]: # ()

[//]: # (## Contributing)

[//]: # ()

[//]: # (Please see [CONTRIBUTING]&#40;CONTRIBUTING.md&#41; for details.)

[//]: # ()

[//]: # (## Security Vulnerabilities)

[//]: # ()

[//]: # (Please review [our security policy]&#40;../../security/policy&#41; on how to report security vulnerabilities.)

[//]: # ()

## Credits

- [SchenkeIo](https://github.com/schenke-io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
