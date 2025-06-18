<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetFarmsteadLocationRequest;
use App\Http\Requests\GetMapTileRequest;
use App\Http\Requests\StoreFarmsteadRequest;
use App\Http\Requests\UpdateFarmsteadRequest;
use App\Models\Farmstead;
use App\Models\Team;
use App\Models\User;
use Clickbar\Magellan\Database\MagellanExpressions\MagellanGeometryExpression;
use Clickbar\Magellan\Database\PostgisFunctions\ST;
use Clickbar\Magellan\IO\Generator\Geojson\GeojsonGenerator;
use Clickbar\Magellan\IO\Parser\Geojson\GeojsonParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FarmsteadController extends Controller
{
    function prepareModel(Farmstead $farmstead): array {
        $arr = $farmstead->toArray();
        $arr['location'] = json_encode($farmstead->location) ?: null;
        return $arr;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        /** @var User $user*/
        $user = Auth::user();
        if ($user->farmstead != null){
        }
        // TODO: Team-Based overview
    }

/**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        /** @var User $user*/
        $user = Auth::user();
        $farmstead = $user->farmstead;
        if ($farmstead != null) {
            return Inertia::render('FarmsteadEdit', ['farmstead' => $farmstead]);
        }

        return Inertia::render('FarmsteadEdit', ['prefill' => [
            'name' => explode(' ', $user->name, 2)[0]."s Boerderij",
            'email' => $user->email,
            'phone_number' => null,
            'location' => null,
            'show_email' => false,
            'show_number' => false,
        ]] );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFarmsteadRequest $request): RedirectResponse
    {
        $rv = $request->validated();
        $user = Auth::user();
        if (isset($rv['create_for'])){
            $user = User::find($rv['create_for']);
        }

        if ($user->farmstead != null) {
            abort(400, "Je hebt al een boerderij toegevoegd!");
        }
        $farm = Farmstead::create($rv);
        $user->farmstead_id = $farm->id;
        $user->saveOrFail();

        if ($rv['farm_reg'] ?? false) {
            return redirect(route('home')); // TODO: Put path for tile editor
        }
        return redirect(route('farmstead.edit', $farm->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(Farmstead $farmstead): void
    {
        // Show the public data about the farmer
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farmstead $farmstead): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->farmstead == null || ($user->farmstead->id != $farmstead->id)) {
            abort(403, "Het is niet jouw boerderij!");
        }
        $fst = $this->prepareModel($farmstead);
        return Inertia::render('FarmsteadEdit', ['farmstead' => $fst]  );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFarmsteadRequest $request, Farmstead $farmstead): RedirectResponse
    {
        $rv = $request->validated();

        //return Inertia::render('DisplayJson', ['json' => gettype($rv['location'])]);

        if (!$farmstead->updateOrFail(array_filter($rv))) {
            abort(400,"EEEEE");
        }
        return redirect(route('farmstead.edit', ['farmstead' => $farmstead->id]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farmstead $farmstead): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->farmstead->id != $farmstead->id) {
            abort(405, "Het is niet jouw boerderij!");
        }

        $farmstead->delete();

        redirect(route('home'));
    }

    public function getLocations(GetMapTileRequest $request): JsonResponse {
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
        $locations = Farmstead::where(
            ST::contains(
                MagellanGeometryExpression::geometry("ST_MakeEnvelope",
                    [$bbox_lonMin, $bbox_latMin, $bbox_lonMax, $bbox_latMax, 4326]),
                'location')
        ,true)->limit(2000)
            ->withMagellanCasts()
            ->get()
            ->map(function (Farmstead $farm) use ($gen) {
                $properties = clone $farm;
                $properties->email = $farm->show_email ? $farm->email : null;
                $properties->number = $farm->show_number ? $farm->number : null;
                $properties->location = null;
                $geometry_obj = $gen->generatePoint($farm->location);

                return [
                    'type' => 'Feature',
                    'geometry' => $geometry_obj,
                    'properties' => $properties,
                ];
            }
         )->all();
        return response()->json($locations);
    }

    public function getLocationsWithResults(GetFarmsteadLocationRequest $request): JsonResponse {
        $req = $request->validated();
        $bbox_latA = (float) $req["blatA"];
        $bbox_lonA = (float) $req["blonA"];
        $bbox_latB = (float) $req["blatB"];
        $bbox_lonB = (float) $req["blonB"];

        if ($req['tid'] == null) {
            abort(400, 'No tid specified');
        }

        /** @var Team $team*/
        $team = Team::find($req['tid']);

        $bbox_latMin = min($bbox_latA, $bbox_latB);
        $bbox_lonMin = min($bbox_lonA, $bbox_lonB);
        $bbox_latMax = max($bbox_latA, $bbox_latB);
        $bbox_lonMax = max($bbox_lonA, $bbox_lonB);

        $gen = new GeojsonGenerator();
        $locations = Farmstead::where(
            ST::contains(
                MagellanGeometryExpression::geometry("ST_MakeEnvelope",
                    [$bbox_lonMin, $bbox_latMin, $bbox_lonMax, $bbox_latMax, 4326]),
                'location')
        ,true)->limit(2000)
            ->withMagellanCasts()
            ->get()
            ->map(function (Farmstead $farm) use ($gen, $team) {
                $properties = clone $farm;
                $properties->email = $farm->show_email ? $farm->email : null;
                $properties->number = $farm->show_number ? $farm->number : null;
                $properties->location = null;
                $milestone = null;
                if($team != null) {
                    $milestone = $farm->evaluateCurrentMilestoneFor($team);
                }
                $properties->color = $milestone?->color;
                $properties->milestone_name = $milestone?->name;
                $geometry_obj = $gen->generatePoint($farm->location);

                return [
                    'type' => 'Feature',
                    'geometry' => $geometry_obj,
                    'properties' => $properties,
                ];
            }
         )->all();
        return response()->json($locations);
    }
}
