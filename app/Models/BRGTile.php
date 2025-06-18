<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $category
 * @property string $gewas
 * @property int $gewascode
 * @property Geometry $geom
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile whereGeom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile whereGewas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile whereGewascode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BRGTile whereId($value)
 * @mixin \Eloquent
 */
class BRGTile extends Model
{
    //
    protected $table = 'brg_tiles';
    protected $casts = [
        'geom' => Geometry::class,
    ];
}
