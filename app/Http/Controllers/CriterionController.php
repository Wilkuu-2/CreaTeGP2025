<?php

namespace App\Http\Controllers;

use App\Enums\CType;
use App\Enums\DType;
use App\Enums\Operator;
use App\Http\Requests\StoreCriterionRequest;
use App\Http\Requests\UpdateCriterionRequest;
use App\Models\Criterion;
use Illuminate\Http\Request;
use App\Models\Milestone;

class CriterionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect(route('milestones.index'));
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
    public function store(StoreCriterionRequest $request)
    {
        $rv = $request->validated();

        $milestone = Milestone::findOrFail($rv['milestone_id']);

        if (!$request->user()->hasTeamPermission($milestone->team, "org:create")) {
            abort(400, "Je hebt geen rechten om criteria te maken.");
        }

        $order =  $milestone->criteria()->count();

        Criterion::create([
            'milestone_id' => $milestone->id,
            'order' => $order,
            'constant' => $rv['constant'],
            'constant_type' => CType::from($rv['constant_type']),
            'operator' => Operator::from($rv['operator']),
            'type' => DType::from($rv['type']),
            'name' => $rv['name'],
            'unit' => $rv['unit'],
        ]);
        return redirect(route('milestones.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Criterion $criterion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Criterion $criterion)
    {
        return redirect(route('milestone:edit', ['milestone' => $criterion->milestone_id]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCriterionRequest $request, Criterion $criterion)
    {
        $team = $criterion->milestone->team;
        if (!$request->user()->hasTeamPermission($team, 'org:edit')) {
            abort(403, "Je hebt niet genoeg rechten");
        }

        $rv = $request->validated();
        $criterion->updateOrFail(array_filter($rv));
        return redirect(route('milestones.index', ['tid' => $team->id] ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Criterion $criterion)
    {
        $team = $criterion->milestone->team;
        if (!$request->user()->hasTeamPermission($team, 'org:delete')) {
            abort(403, "Je hebt niet genoeg rechten");
        }

        $criterion->delete();
        return redirect(route('milestones.index', ['tid' => $team->id] ));
        // TODO: Reorder
    }
}
