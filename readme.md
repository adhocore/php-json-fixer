## adhocore/json-fixer

PHP library to fix Truncated JSON data by padding contextual counterpart to the end. Works with PHP5.4 or above.

[![Latest Version](https://img.shields.io/github/release/adhocore/php-json-fixer.svg?style=flat-square)](https://github.com/adhocore/php-json-fixer/releases)
[![Travis Build](https://travis-ci.com/adhocore/php-json-fixer.svg?branch=master)](https://travis-ci.com/adhocore/php-json-fixer?branch=master)
[![Scrutinizer CI](https://img.shields.io/scrutinizer/g/adhocore/php-json-fixer.svg?style=flat-square)](https://scrutinizer-ci.com/g/adhocore/php-json-fixer/?branch=master)
[![Codecov branch](https://img.shields.io/codecov/c/github/adhocore/php-json-fixer/master.svg?style=flat-square)](https://codecov.io/gh/adhocore/php-json-fixer)
[![StyleCI](https://styleci.io/repos/141589074/shield)](https://styleci.io/repos/141589074)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/intent/tweet?text=Rescue+and+fix+truncated+JSON+data+in+PHP&url=https://github.com/adhocore/php-json-fixer&hashtags=php,json,jsonfixer,fixjson)
[![Support](https://img.shields.io/static/v1?label=Support&message=%E2%9D%A4&logo=GitHub)](https://github.com/sponsors/adhocore)
<!-- [![Donate 15](https://img.shields.io/badge/donate-paypal-blue.svg?style=flat-square&label=donate+15)](https://www.paypal.me/ji10/15usd)
[![Donate 25](https://img.shields.io/badge/donate-paypal-blue.svg?style=flat-square&label=donate+25)](https://www.paypal.me/ji10/25usd)
[![Donate 50](https://img.shields.io/badge/donate-paypal-blue.svg?style=flat-square&label=donate+50)](https://www.paypal.me/ji10/50usd) -->


- Zero dependency (no vendor bloat).

**It is a work in progress and might not cover all edge cases.** It would be great if you try it out, open some issues or contribute.

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
// {"b":[1,[{"b":1,"c":null}]]}

// For batch fixing, you can just reuse same fixer instance:
$fixer = new Fixer;

$fixer->fix('...');
$fixer->fix('...');
// ...
```

## Error

If there's error and fixer cant fix the JSON for some reason, it will throw a `RuntimeException`.
You can disable this behavior by passing silent flag (2nd param) to `fix()` in which case original input is returned:

```php
(new Fixer)->silent()->fix('invalid');
// 'invalid'

(new Fixer)->silent(true)->fix('invalid');
// 'invalid'

(new Fixer)->silent(false)->fix('invalid');
// RuntimeException
```

## Missing Value

By default missing values are padded with `null`. You can change it passing desired value to `missingValue()`:

```php
// key b is missing value and is padded with `null`
$json = (new Fixer)->fix('{"a":1,"b":');
// {"a":1,"b":null}

// key b is missing value and is padded with `true`
$json = (new Fixer)->missingValue(true)->fix('{"a":1,"b":');
// {"a":1,"b":true}

// key b is missing value and is padded with `"truncated"`
// Note that you can actually inject a whole new JSON subset as 3rd param
// but that should be a valid JSON segment and is not checked by fixer.
$json = (new Fixer)->missingValue('"truncated"')->fix('{"a":1,"b":');
// {"a":1,"b":"truncated"}
```

## Todo

- [ ] Configurable missing value as per context (options)
