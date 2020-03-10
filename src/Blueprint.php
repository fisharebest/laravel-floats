<?php
/**
 * @package   fisharebest/laravel-floats
 * @copyright 2020 "Greg Roach" <greg@subaqua.co.uk>
 * @licence   MIT
 */
namespace Fisharebest\LaravelFloats;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;

class Blueprint extends BaseBlueprint
{
    /**
     * Create a new float column on the table.
     *
     * @param  string    $column
     * @param  int|null  $total
     * @param  int|null  $places
     * @param  bool|null $unsigned
     *
     * @return ColumnDefinition
     */
    public function float($column, $total = null, $places = null, $unsigned = false)
    {
        return $this->addColumn('float', $column, compact('total', 'places', 'unsigned'));
    }
}
