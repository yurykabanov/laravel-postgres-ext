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

PostgreSQL supports [several index types](https://www.postgresql.org/docs/current/static/sql-createindex.html): **btree**, **hash**, **gist**, **spgist**, **gin**, and **brin** (as for version 9.5) and other index-related features (for example, index can be created concurrently, i.e. without table locking). This package supports creation of all currently supported indexing methods (defined in `PostgresGrammar::SUPPORTED_INDEX_METHODS`).

Indexes can be created using the same syntax as original one:
```php
$table->index('column_name');
```
but it now accepts additional parameters:
```php
$table->index('column_name', 'index_name', $methodName, $arrayOfOptions);
```
where `$methodName` is one of methods listed before and `$arrayOfOptions` is array with different options (like concurrency and uniqueness).

**Examples**:
```php
$table->index('column_name', 'index_name', 'gist', [ 'concurrently' => true ]); // create index concurrently ... using gist ...
$table->index('column_name', 'index_name', 'gin',  [ 'unique' => true ]);       // create unique index ... using btree ...
```

**Note**, that there's two ways of making column unique: using **constraint** and **index** ([more information](http://stackoverflow.com/questions/23542794/postgres-unique-constraint-vs-index)). Laravel uses **constraint** to ensure uniqueness of column, this behavior stays the same for `$table->unique()` but you can also create **unique index** using `$table->index($col, $index, $method, [ 'unique' => true ])`.

