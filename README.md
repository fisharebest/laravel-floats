[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Latest Stable Version](https://poser.pugx.org/fisharebest/laravel-floats/v/stable.svg)](https://packagist.org/packages/fisharebest/laravel-floats)
[![Build Status](https://travis-ci.org/fisharebest/laravel-floats.svg?branch=master)](https://travis-ci.org/fisharebest/laravel-floats)
[![Coverage Status](https://coveralls.io/repos/fisharebest/laravel-floats/badge.svg?branch=master&service=github)](https://coveralls.io/github/fisharebest/laravel-floats?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/laravel-floats/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/laravel-floats/?branch=master)

# laravel-floats

This package allows database migrations in Laravel 5 to create `FLOAT` columns in MySQL.

## Huh? You mean that Laravel does not support floating point columns?

Sadly no.  It only supports *double-precision* floating point columns.
If you want *single-precision* floating point, you need to use `DB::raw()`.

This is despite the Laravel documentation at <https://laravel.com/docs/5.7/migrations>
which states:
                
> `$table->float('amount', 8, 2);` FLOAT equivalent column with a precision (total digits) and scale (decimal digits).

> `$table->double('amount', 8, 2);` DOUBLE equivalent column with a precision (total digits) and scale (decimal digits).
                
You can read all about it at <https://github.com/laravel/framework/issues/3151>
and many other issues.

| In your migration              | Laravel 5.0 - 5.7 | With this package |
| ------------------------------ | ----------------- | ----------------- |
| `$table->float('col');`        | `DOUBLE(8,2)`     | `FLOAT`           |
| `$table->float('col', 0);`     | `DOUBLE`          | `FLOAT`           |
| `$table->float('col', 5);`     | `DOUBLE(5,2)`     | `FLOAT`           |
| `$table->float('col', 5, 3);`  | `DOUBLE(5,3)`     | `FLOAT(5,3)`      |
| `$table->double('col');`       | `DOUBLE`          | `DOUBLE`          |
| `$table->double('col', 0);`    | `DOUBLE`          | `DOUBLE`          |
| `$table->double('col', 5);`    | `DOUBLE`          | `DOUBLE`          |
| `$table->double('col', 5, 3);` | `DOUBLE(5,3)`     | `DOUBLE(5,3)`     |


## Installation
 
```
composer require fisharebest/laravel-floats
```

Package discovery takes care of everything on Laravel 5.5 and later.
If you're using Laravel 5.4 or earlier, you'll need to replace an alias in `config/app.php`.

```
'aliases' => [
    ...
    'Schema' => \Fisharebest\LaravelFloats\Schema::class,
    ....
]
```

## How does this package work?

We extend the MySQL Grammar, modify the blueprint for `float()`, and then
bind the updated grammar back into the IoC container.
