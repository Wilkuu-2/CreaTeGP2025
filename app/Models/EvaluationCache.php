<?php
namespace App\Models;

use App\Models\Criterion;
use Illuminate\Support\Facades\Log;


class EvaluationCache {
    public array $milestones = array();
    public array $criteria = array();

    /**
     * @param callable(): bool $fn
     */
    function useCriterion(Criterion $criterion, callable $fn): bool {
        $crit = $this->criteria[$criterion->id] ?? null;
        if ($crit != null) {
            Log::debug("Cache hit on criterion: " . $criterion->id);
            return $crit == 2;
        } else {
            $crit = $fn($criterion);
            $this->criteria[$criterion->id] = $crit ? 2 : 1;
            return $crit;
        }
    }
    /**
     * @param callable(): bool $fn
     */
    function useMilestone(Milestone $milestone, callable $fn): bool {
        $mst = $this->milestones[$milestone->id] ?? null;
        if ($mst != null) {
            Log::debug("Cache hit on milestone: " . $milestone->id);
            return $mst == 2;
        } else {
            $mst = $fn($milestone);
            $this->milestones[$milestone->id] = $mst ? 2 : 1;
            return $mst;
        }
    }
    /**
     * @param callable(): bool $fn
     */
    function useMilestoneId(int $id, callable $fn): bool {
        $mst = $this->milestones[$id] ?? null;
        if ($mst != null) {
            Log::debug("Cache hit on milestone: " . $id);
            return $mst == 2;
        } else {
            $mst = $fn(Milestone::find($id));
            $this->milestones[$id] = $mst ? 2 : 1;
            return $mst;
        }
    }
}
