<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property int $team_id
 * @property int $hold_duration
 * @property bool $needs_approval
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $order
 * @property string $name
 * @property bool $do_map
 * @property bool $is_any
 * @property string $color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Criterion> $criteria
 * @property-read int|null $criteria_count
 * @property-read \App\Models\Team $team
 * @method static \Database\Factories\MilestoneFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereDoMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereHoldDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereIsAny($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereNeedsApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Milestone extends Model
{
    /** @use HasFactory<\Database\Factories\MilestoneFactory> */
    use HasFactory;
    public $incrementing = true;
    public $primary_key = 'id';
    protected $fillable = ['hold_duration', 'needs_approval', 'order', 'color', 'do_map', 'is_any', 'name', 'team_id'];

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function criteria(): HasMany {
        return $this->hasMany(Criterion::class);
    }

    public function evaluateFor(Farmstead $farmstead, EvaluationCache $cache = new EvaluationCache()): bool {
        return $cache->useMilestone($this, function () use ($farmstead, $cache) {
            foreach ($this->criteria as $crit) {
                $ceval = $crit->evaluateFor($farmstead, $cache);
                if ($ceval && $this->is_any) {
                    return true;
                } elseif (!$ceval && !$this->is_any) {
                    return false;
                }
            }
            return !$this->is_any;}
        );
    }

}
