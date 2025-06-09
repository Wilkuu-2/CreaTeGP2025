<?php

namespace App\Http\Controllers;

use App\Models\Kadaster;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Clickbar\Magellan\IO\Generator\Geojson\GeojsonGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Clickbar\Magellan\Database\PostgisFunctions\ST;
use Clickbar\Magellan\IO\Parser\WKB\WKBParser;
use Illuminate\Support\Facades\DB;

class KadasterController extends Controller
{

    /**
     * Get all tiles within an area.
     */
    public function getArea(Request $req)
    {
        $validator = Validator::make($req->all(),[
            'blatA' => 'required|numeric',
            'blonA' => 'required|numeric',
            'blatB' => 'required|numeric',
            'blonB' => 'required|numeric',
            'srid' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $bbox_latA = (float) $req->input("blatA");
        $bbox_lonA = (float) $req->input("blonA");
        $bbox_latB = (float) $req->input("blatB");
        $bbox_lonB = (float) $req->input("blonB");
        $srid = intval($req->input("srid", 4326));

        $bbox_latMin = min($bbox_latA, $bbox_latB);
        $bbox_lonMin = min($bbox_lonA, $bbox_lonB);
        $bbox_latMax = max($bbox_latA, $bbox_latB);
        $bbox_lonMax = max($bbox_lonA, $bbox_lonB);


        // $bboxMin = Point::make(min($bbox_latA, $bbox_latB), min($bbox_lonA, $bbox_lonB), null, null, $srid);
        // $bboxMax = Point::make(max($bbox_latA, $bbox_latB), max($bbox_lonA, $bbox_lonB), null, null, $srid);

        $gen = new GeojsonGenerator();
        Db::enableQueryLog();
        $tiles = DB::table("kadaster")->select()
            ->addSelect(ST::transform('geometry', $srid)->as('trans_geo'))
            ->whereRaw('public.ST_Intersects(
                public.ST_Transform(
                    public.ST_MakeEnvelope(?::float,?::float,?::float,?::float,?::int),
                    ?::int),
                "geometry") AND areavalue > 2000',
            [$bbox_lonMin, $bbox_latMin, $bbox_lonMax, $bbox_latMax, $srid, Kadaster::$srid])

            // ->where(
            //     ST::contains(
            //         ST::transform(
            //             St::setSrid(ST::makeBox2D($bboxMin, $bboxMax), $srid)
            //             , Kadaster::$srid),
            //         'geometry'))
            ->limit(10000)
            ->get()
            ->map(function (object $tile) use ($gen) {
                $parser = app(WKBParser::class);
                /** @var Geometry */
                $geom = $parser->parse($tile->trans_geo);
                $geometry_obj = $gen->generate($geom);
                $json = [
                     'type' => 'Feature',
                     'geometry' => $geometry_obj,
                     'properties' => [
                        'id' => $tile->ogc_fid,
                        'localid' => $tile->localid,
                        'area' => $tile->areavalue,
                    ],
                ];
                return $json;
            })->all();
        DB::disableQueryLog();
        $obj = [
            "type" => "FeatureCollection",
            "features" => $tiles,
        ];

    //  return Inertia::render("DisplayJson", ['json' => $obj, 'name' => 'GeoJson test' ]);

        return response()->json($obj);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Kadaster $kadaster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kadaster $kadaster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kadaster $kadaster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kadaster $kadaster)
    {
        //
    }
}
