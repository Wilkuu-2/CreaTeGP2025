<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ogc_fid
 * @property string $gml_id
 * @property float|null $areavalue
 * @property string|null $areavalue_uom
 * @property string|null $beginlifespanversion
 * @property string|null $endlifespanversion
 * @property string|null $localid
 * @property string|null $namespace
 * @property int|null $label
 * @property string|null $nationalcadastralreference
 * @property string|null $validfrom
 * @property string|null $validto
 * @property Geometry|null $geometry
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereAreavalue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereAreavalueUom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereBeginlifespanversion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereEndlifespanversion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereGeometry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereGmlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereLocalid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereNamespace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereNationalcadastralreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereOgcFid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereValidfrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kadaster whereValidto($value)
 * @mixin \Eloquent
 */
class Kadaster extends Model
{
    static int $srid = 4258;
    protected $table = 'kadaster';
    protected $casts = [
        'geometry' => Geometry::class,
    ];

}
