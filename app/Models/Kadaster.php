<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;

class Kadaster extends Model
{
    static int $srid = 4258;
    protected $table = 'kadaster';
    protected $casts = [
        'geometry' => Geometry::class,
    ];

}
