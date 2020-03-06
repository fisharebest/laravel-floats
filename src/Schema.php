<?php
/**
 * @package   fisharebest/laravel-floats
 * @copyright 2020 "Greg Roach" <greg@subaqua.co.uk>
 * @licence   MIT
 */
namespace Fisharebest\LaravelFloats;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema as BaseSchema;

class Schema extends BaseSchema
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string $name
     *
     * @return Builder
     */
    public static function connection($name)
    {
        /** @var Connection $connection */
        $connection = static::$app['db']->connection($name);

        if ($connection instanceof MySqlConnection) {
            $mysql_grammar = $connection->withTablePrefix(new MySqlGrammar());
            
            $connection->setSchemaGrammar($mysql_grammar);

            $schema_builder = $connection->getSchemaBuilder();

            // Use our own version of Blueprint.
            // Note that the constructor signature changed between Laravel 5.6 and 5.7,
            // so we use variable arguments to work with both.
            $schema_builder->blueprintResolver(function (...$args) {
                return new Blueprint(/** @scrutinizer ignore-type */ ...$args);
            });

            return $schema_builder;
        }

        return $connection->getSchemaBuilder();
    }

    /**
     * Get a schema builder instance for the default connection.
     *
     * @return Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::connection(null);
    }
}
