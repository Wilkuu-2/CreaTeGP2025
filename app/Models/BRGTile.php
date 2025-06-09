<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;

class BRGTile extends Model
{
    //
    protected $table = 'brg_tiles';
    protected $casts = [
        'geom' => Geometry::class,
    ];
}
