# device-detector-factory

[![Latest Stable Version](https://poser.pugx.org/mimmi20/device-detector-factory/v/stable?format=flat-square)](https://packagist.org/packages/mimmi20/device-detector-factory)
[![Latest Unstable Version](https://poser.pugx.org/mimmi20/device-detector-factory/v/unstable?format=flat-square)](https://packagist.org/packages/mimmi20/device-detector-factory)
[![License](https://poser.pugx.org/mimmi20/device-detector-factory/license?format=flat-square)](https://packagist.org/packages/mimmi20/device-detector-factory)

## Code Status

[![codecov](https://codecov.io/gh/mimmi20/device-detector-factory/branch/master/graph/badge.svg)](https://codecov.io/gh/mimmi20/device-detector-factory)
[![Coverage Status](https://coveralls.io/repos/github/mimmi20/device-detector-factory/badge.svg?branch=master)](https://coveralls.io/github/mimmi20/device-detector-factory?branch=master)
[![Average time to resolve an issue](https://isitmaintained.com/badge/resolution/mimmi20/device-detector-factory.svg)](https://isitmaintained.com/project/mimmi20/device-detector-factory "Average time to resolve an issue")
[![Percentage of issues still open](https://isitmaintained.com/badge/open/mimmi20/device-detector-factory.svg)](https://isitmaintained.com/project/mimmi20/device-detector-factory "Percentage of issues still open")
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fmimmi20%2Fdevice-detector-factory%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/mimmi20/device-detector-factory/master)

## Introduction

This component provides a factory to create a matomo device detector with a config.

## Requirements

This library requires PHP 8.3+.

## Installation

Run

```shell
composer require mimmi20/device-detector-factory
```

## Usage with Laminas and Mezzio

You'll need to add configuration and register the services you'd like to use. There are number of ways to do that
but the recommended way is to create a new config file `config/autoload/detector.config.php`

## Configuration

config/autoload/detector.config.php

```php
<?php
return [
    'device-detector' => [
        'discard-bot-information' => true, // Optional: defaults to false
        'skip-bot-detection' => true, // Optional: defaults to false
        'cache' => 'data-cache', // Optional, may be a string or an instance of \Laminas\Cache\Storage\StorageInterface
    ],
];
```

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).
