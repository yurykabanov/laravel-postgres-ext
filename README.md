# Laravel 5.x extended PostgreSQL driver
[![Build Status](https://travis-ci.org/yurykabanov/laravel-postgres-ext.svg?branch=master)](https://travis-ci.org/yurykabanov/laravel-postgres-ext)

This project was inspired by features PostgreSQL supports and Laravel does not. Unfortunately, such features are not accepted in official repository (like [this one](https://github.com/laravel/framework/pull/9866)) and developers are told to use raw queries that is completely wrong solution in my opinion.

## Requirements

1. PHP >= 5.6 or HHVM (probably it will work on PHP 5.5.9, which is a minimal requirement of Laravel, but it was not tested since PHPUnit 5 requires at least PHP 5.6)
2. PostgreSQL. Obviously it has to support particular feature you want to use. For example, to use **views** it has to be at least 9.1, to use **upsert** it has to be at least 9.5 (current stable). 

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

### Views

PostgreSQL supports **views** (since version 9.1) and **materialized view** (since version 9.3). These can be created using `DB::statement()` but it's more convenient to use some aliases to manage views.
 
Views can be created using following statements:
```php
// create non-materialized view using specified select statement
Schema::createView('some_view', 'select 1 as some_value');
// create materialized view using specified select statement
Schema::createView('some_view', 'select 1 as some_value', true);
```
and dropped:
```php
// create non-materialized view using specified select statement
Schema::dropView('some_view');
```

So far it doesn't support some query builders since view's select statement could be (and usually *is*) very complicated.

### Jsonb operators

Laravel *does* support **jsonb** type and is supposed to support jsonb operators like `?`, `?|` and `?&` but it is impossible to use them in queries since they are treated as parameters in prepared statements. This packages automatically wraps these operators in appropriate functions (Note that '?|' also used for other types -- this behavior is not supported at this moment).

### Group by grouping sets, rollup, cube

Available group by expressions described [in official documentation](https://www.postgresql.org/docs/devel/static/queries-table-expressions.html).

```php
// GROUP BY GROUPING SETS ((brand), (size), ())
DB::table('some_table')->groupByGroupingSets('brand', 'size', null);

// GROUP BY ROLLUP (e1, e2, e3)
DB::table('some_table')->groupByRollup('brand', 'size', null);

// GROUP BY CUBE (e1, e2, e3)
DB::table('some_table')->groupByCube('brand', 'size', null);
```
