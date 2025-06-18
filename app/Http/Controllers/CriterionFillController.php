<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\CriterionFill;
use App\Models\Milestone;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CriterionFillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        /** @var */
        $team = $user->currentTeam;
        $rv = $request->validate([
            'mode_override' => 'integer',
        ]);
        $override = $rv['mode_override'] ?? null;

        $isOrganiser = $user->hasTeamRole($team, 'Organiser') || $user->hasTeamRole($team, 'Admin');
        $isFarmer = $user->farmstead != null;

        if ($isOrganiser && ($override == null || $override != 1)){
            return redirect(route('fills.organizaion'));
        } elseif ($isFarmer) {
            return redirect(route('fills.farmer'));
        } else {
            return redirect(route('fills.nofarm'));
        }

    }

    public function farmerIndex(Request $request) {
        /** @var User $user */
        $user = Auth::user();
        $team = $user->currentTeam;
        $farmstead = $user->farmstead;

        if ($farmstead == null) {
            redirect(route('fills.nofarm'));
        }

        if ($team == null) {
            return abort(400, "Je hebt hoort bij geen organisatie, dus je hebt geen data om in te vullen.");
        }
        $milestones = Milestone::whereTeamId($team->id)->orderBy('order')->get();
        $mids = $milestones->pluck('id');
        $criteria = Criterion::whereIn('milestone_id', $mids)->get();
        $crids = $criteria->pluck('id');
        $fills = CriterionFill::whereIn('criterion_id', $crids)->where('farmstead_id', $farmstead->id)->get();

        if ($fills->count() < $crids->count()){
            $missing = $crids->diff($fills->pluck('criterion_id'));
            foreach ( $missing  as $id) {
                /** @var Criterion $crit*/
                $crit = $criteria->first(function(Criterion $value, int $key) use ($id)  {
                    return $value->id == $id;
                });
                $fills[] = $crit->createFill($farmstead);
            }
        }

        return Inertia::render('FillsFarmer', ['milestones' => $milestones, 'criteria' => $criteria, 'fills' => $fills, 'farmstead' => $farmstead]);
        // return Inertia::render('DisplayJson', [ 'json' => ['mode' => 'farm', 'fills' => $fills, 'criteria' => $criteria]]);

    }

    public function organizationIndex(Request $request) {
        return Inertia::render('DisplayJson', [ 'json' => ['mode' => 'org']]);
    }

    public function noFarmPage(Request $request) {
        return Inertia::render('DisplayJson', [ 'json' => ['mode' => 'nofarm']]);
    }


    /**
     * Display the specified resource.
     */
    public function show(CriterionFill $criterionFill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CriterionFill $criterionFill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $farmstead_id, int $criterion_id): JsonResponse {
        $uf = Auth::user()->farmstead;
        if ($uf == null || $uf->id != $farmstead_id ) {
            abort(403, "Je hebt geen recht om de data van dit boerderij aan te passen.");
        }
        $rv = $request->validate([
            'bool1' => 'bool|nullable',
            'bool2' => 'bool|nullable',
            'double1' => 'numeric|nullable',
            'double2' => 'numeric|nullable',
            'int1' => 'integer|nullable',
            'int2' => 'integer|nullable',
        ]);


        /** @var CriterionFill $fill*/
        $fill = CriterionFill::whereFarmsteadId($farmstead_id)->whereCriterionId($criterion_id)->first();
        $fill->update(array_filter($rv));
        return response()->json(['result' => $rv]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CriterionFill $criterionFill)
    {
        //
    }
}
