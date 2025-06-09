<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Models\Criterion;
use App\Models\Milestone;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MilestoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            "tid" => "integer",
        ]);
        $tid = intval($request->input("tid", Auth::user()->current_team_id));

        $milestones = Milestone::whereTeamId($tid)->orderBy('order')->get();
        $mids = $milestones->pluck('id');
        $criteria = Criterion::whereIn('milestone_id', $mids)->get();

        return Inertia::render("Milestones", [
                'milestones' => $milestones,
                'criteria' => $criteria,
                'tid' => $tid,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            "tid" => "integer",
        ]);
        $tid = intval($request->input("tid", Auth::user()->current_team_id));

        $milestones = Milestone::whereTeamId($tid)->orderBy('order')->get();
        $mids = $milestones->pluck('id');
        $criteria = Criterion::whereIn('milestone_id', $mids)->get();

        return Inertia::render("Milestones", [
            'milestones' => $milestones,
            'criteria' => $criteria,
            'new_milestone' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMilestoneRequest $request)
    {

        $rv = $request->validated();
        $tid = $rv['tid'];
        $team = Team::findOrFail($tid);
        if (!$request->user()->hasTeamPermission($team, 'org:create')) {
            abort(403, "Je hebt niet genoeg rechten");
        }

       $order = Milestone::whereTeamId($rv['tid'])->count();

        Milestone::create([
            'team_id' => $rv['tid'],
            'order' => $order,
            'hold_duration' => $rv['hold_duration'] ?: 0,
            'needs_approval' => $rv['needs_approval'] ?: false,
            'color' => $rv['color'] ?: '\#000000',
            'name' => $rv['name'],
            'is_any' => $rv['is_any'],
            'do_map' => $rv['do_map'],
        ]);

        return redirect(route('milestones.index', ['tid' => $rv['tid']] ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Milestone $milestone)
    {
        $milestones = Milestone::whereTeamId($milestone->team_id)->orderBy('order')->get();
        $mids = $milestones->pluck('id');
        $criteria = Criterion::whereIn('milestone_id', $mids)->get();
        //
        return Inertia::render("Milestones", [
                'milestones' => $milestones,
                'criteria' => $criteria,
                'tid' => $milestone->team_id,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Milestone $milestone)
    {
        $milestones = Milestone::whereTeamId($milestone->team_id)->orderBy('order')->get();
        $mids = $milestones->pluck('id');
        $criteria = Criterion::whereIn('milestone_id', $mids)->get();
        //
        return Inertia::render("Milestones", [
                'milestones' => $milestones,
                'criteria' => $criteria,
                'editing_milestone' => $milestone->id,
                'tid' => $milestone->team_id,
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMilestoneRequest $request, Milestone $milestone)
    {
        $team = Team::findOrFail($milestone->team_id);
        if (!$request->user()->hasTeamPermission($team, 'org:edit')) {
            abort(403, "Je hebt niet genoeg rechten");
        }

        $rv = $request->validated();
        $milestone->updateOrFail(array_filter($rv));
        return redirect(route('milestones.index', ['tid' => $milestone->team_id] ));
    }

    /**
    * Update the order of criteria and milestones
    */
    public function reorder(Request $request) {
        // TODO: Make sure that the criteria id's are unique
       $obj =  $request->validate([
                'tid' => 'integer|required|exists:teams,id',
                'order' => 'array|required',
                'order.*' => 'array|required',
                'order.*.id' => 'exists:milestones,id|required',
                'order.*.criteria' => 'array',
                'order.*.criteria.*' => 'exists:criteria,id',
            ]
        );

        DB::beginTransaction();
        $order = $obj['order'];
        $tid = $obj['tid'];
        foreach ($order as $index => $mobj) {
            $mid = $mobj['id'];
            $milestone = Milestone::findOrFail($mid);
            $milestone->order = $index;
            if ($milestone->team_id != $tid) {
                DB::rollBack();
                abort(400, "Found milestone that does not belong to the order");
            }
            $milestone->save();

            $ccount = 0;
            foreach ($mobj['criteria'] as $crit_id) {
                $criterion = Criterion::find($crit_id);
                if ($criterion->milestone->team_id != $tid) {
                    DB::rollBack();
                    abort(400, "Found criterion that does not belong to the order");
                }
                $criterion->order = $ccount;
                $criterion->mid = $milestone->id;
                $criterion->save();
                $ccount += 1;
            }

        }
        DB::commit();
        return redirect(route('milestones.index', ['tid' => $tid] ));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Milestone $milestone)
    {
        $tid = $milestone->team_id;
        $team = Team::findOrFail($tid);
        if (!$request->user()->hasTeamPermission($team, 'org:delete')) {
            abort(403, "Je hebt niet genoeg rechten");
        }

        $milestone->delete();
        return redirect(route('milestones.index', ['tid' => $tid] ));
    }
}
