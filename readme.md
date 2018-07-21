## adhocore/json-fixer

PHP library to fix Truncated JSON data by padding contextual counterpart to the end.

[![Latest Version](https://img.shields.io/github/release/adhocore/php-json-fixer.svg?style=flat-square)](https://github.com/adhocore/php-json-fixer/releases)
[![Travis Build](https://img.shields.io/travis/adhocore/php-json-fixer/master.svg?style=flat-square)](https://travis-ci.org/adhocore/php-json-fixer?branch=master)
[![Scrutinizer CI](https://img.shields.io/scrutinizer/g/adhocore/php-json-fixer.svg?style=flat-square)](https://scrutinizer-ci.com/g/adhocore/php-json-fixer/?branch=master)
[![Codecov branch](https://img.shields.io/codecov/c/github/adhocore/php-json-fixer/master.svg?style=flat-square)](https://codecov.io/gh/adhocore/php-json-fixer)
[![StyleCI](https://styleci.io/repos/141589074/shield)](https://styleci.io/repos/141589074)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


## Installation
```bash
composer require adhocore/json-fixer
```

## Usage
```php
use Ahc\Json\Fixer;

$json = (new Fixer)->fix('{"a":1,"b":2');
// {"a":1,"b":2}

$json = (new Fixer)->fix('{"a":1,"b":true,');
// {"a":1,"b":true}

$json = (new Fixer)->fix('{"b":[1,[{"b":1,"c"');
// {"b":[1,[{"b":1,"c":true}]]}
```
