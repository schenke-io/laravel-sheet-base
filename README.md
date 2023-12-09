# Laravel Sheet Base

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-sheet-base/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-sheet-base/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-sheet-base/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/schenke-io/laravel-sheet-base/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-sheet-base.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-sheet-base)

This Laravel package contains classes to build pipelines between files for situations when data do not change very
often.

On one sides there are endpoints which are read and on the other side
of each pipeline thre is a writer which writes the result.
The pipeline has also a schema to describe its tabular data format.

There are readers for files, ararys, collections,
eleoquent models, cloud files and Google sheets.

The writers can handle json, neon, yaml and php files but also
Laravel Language files. The php files can be used
as config or traits.

Typical applications are:

* collecting data from csv files and write them to json, yaml or neon
* read translations from various sources and build language files from it
* build files to be used in Seeding, Backup or for Laravel Sushi.

## Installation

You can install the package via composer:

```bash
composer require schenke-io/laravel-sheet-base
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-sheet-base-config"
```

## Usage

```php

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
