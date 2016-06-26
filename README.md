# Laravel 5.x extended PostgreSQL driver
[![Build Status](https://travis-ci.org/yurykabanov/laravel-postgres-ext.svg?branch=master)](https://travis-ci.org/yurykabanov/laravel-postgres-ext)

This project was inspired by features PostgreSQL supports and Laravel does not. Unfortunately, such features are not accepted in official repository (like [this one](https://github.com/laravel/framework/pull/9866)) and developers are told to use raw queries that is completely wrong solution in my opinion.

## Installation

1. Run `composer require yurykabanov/laravel-postgres-ext` to install this package.
2. Change database service provider from original `Illuminate\Database\DatabaseServiceProvider::class` to `YuryKabanov\Database\DatabaseServiceProvider::class`.
3. In models instead of `Illuminate\Database\Eloquent\Model` extend `YuryKabanov\Database\Eloquent\Model`.

## Features available

### UPSERT

UPSERT (INSERT ON CONFLICT UPDATE) is supported by PostgreSQL since version 9.5 and can be performed by calling
```php
Model::upsert($arrayOfAttibutes, $uniqueField)
```
Like original **insert** method **upsert** can manage multiple records.

### JSONB

TODO

### Various index types

TODO

