<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function getNameList(Request $request): JsonResponse {
        return response()->json(Team::select('id', 'name')->has('milestones')->get());
    }
}
