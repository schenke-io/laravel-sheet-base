# Laravel Sheet Base

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-sheet-base/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-sheet-base/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-sheet-base/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/schenke-io/laravel-sheet-base/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)


This Laravel package facilitates the creation of pipelines for scenarios 
where data changes infrequently. The pipelines connect endpoints that 
serve as readers to those with writers, responsible for generating the 
output. Each pipeline is equipped with a schema to define its tabular data format.

Readers are available for files, arrays, collections, Eloquent models, 
cloud files, and Google Sheets. On the writing side, the package supports 
JSON, Neon, YAML, PHP files, and Laravel Language files. The PHP files can 
serve as configurations or traits.

Notably, all generated files contain remarks indicating the reference to 
the file that produced them, emphasizing that editing these files is not 
recommended. This ensures clarity and discourages unnecessary modifications.

Typical applications are:

* collecting data from csv files and write them to json, yaml or neon
* read translations from various sources and build language files from it
* build files to be used in seeding, backup , configuration or for Laravel Sushi.

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
