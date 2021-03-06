<?php
/**
 * @package   fisharebest/laravel-floats
 * @copyright 2020 "Greg Roach" <greg@subaqua.co.uk>
 * @licence   MIT
 */
namespace Fisharebest\LaravelFloats;

use Illuminate\Database\Schema\Grammars\MySqlGrammar as BaseMySqlGrammar;
use Illuminate\Support\Fluent;

class MySqlGrammar extends BaseMySqlGrammar
{
    /**
     * Create the column definition for a float type.
     *
     * @param  Fluent $column
     *
     * @return string
     */
    protected function typeFloat(Fluent $column)
    {
        if ($column->total && $column->places) {
            return "float({$column->total}, {$column->places})";
        }

        return 'float';
    }
}
