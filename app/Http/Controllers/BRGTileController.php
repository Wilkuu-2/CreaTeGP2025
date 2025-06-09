<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetMapTileRequest;
use App\Models\BRGTile;
use Clickbar\Magellan\Data\Boxes\Box2D;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Clickbar\Magellan\Data\Geometries\Polygon;
use Clickbar\Magellan\Database\MagellanExpressions\MagellanGeometryExpression;
use Clickbar\Magellan\IO\Generator\Geojson\GeojsonGenerator;
use Clickbar\Magellan\Database\PostgisFunctions\ST;

class BRGTileController extends Controller
{
    //

    public function getTiles(GetMapTileRequest $request) {
        //
        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ], 400);
        // }

        $req = $request->validated();
        $bbox_latA = (float) $req["blatA"];
        $bbox_lonA = (float) $req["blonA"];
        $bbox_latB = (float) $req["blatB"];
        $bbox_lonB = (float) $req["blonB"];

        $bbox_latMin = min($bbox_latA, $bbox_latB);
        $bbox_lonMin = min($bbox_lonA, $bbox_lonB);
        $bbox_latMax = max($bbox_latA, $bbox_latB);
        $bbox_lonMax = max($bbox_lonA, $bbox_lonB);

        $gen = new GeojsonGenerator();
        $tiles = BRGTile::whereNotIn('category', ['Landschapselement','Natuurterein'])
            ->where(
                ST::intersects(
                    MagellanGeometryExpression::geometry("ST_MakeEnvelope",[$bbox_lonMin, $bbox_latMin, $bbox_lonMax, $bbox_latMax, 4326]), 'geom'), true)
            ->limit(10000)
            ->withMagellanCasts()
            ->get()
            ->map(function (BRGTile $tile) use ($gen) {
                /** @var Geometry */
                $geometry_obj = $gen->generate($tile->geom);
                $json = [
                     'type' => 'Feature',
                     'geometry' => $geometry_obj,
                     'properties' => [
                        'id' => $tile->id,
                        'category' => $tile->category,
                        'gewas' => $tile->gewas,
                        'gewascode' => $tile->gewascode,
                    ],
                ];
                return $json;
            })->all();

        return response()->json($tiles);
    }
}
