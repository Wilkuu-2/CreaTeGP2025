<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use App\Enums\CType;
use App\Enums\DType;
use App\Enums\Operator;

/**
 *
 *
 * @property int $id
 * @property int $milestone_id
 * @property int $order
 * @property Operator $operator
 * @property DType $type
 * @property CType $constant_type
 * @property string $constant
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $unit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CriterionFill> $fills
 * @property-read int|null $fills_count
 * @property-read \App\Models\Milestone $milestone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereConstant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereConstantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereMilestoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criterion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Criterion extends Model
{
    protected $fillable = ['milestone_id','order', 'unit', 'operator', 'type', 'constant', 'constant_type', 'name'];
    protected $casts = [
        'operator' => Operator::class,
        'type' => DType::class,
        'constant_type' => CType::class,
    ];

    public function milestone(): BelongsTo {
        return $this->belongsTo(Milestone::class, 'milestone_id');
    }

    public function fills(): HasMany {
        return $this->hasMany(CriterionFill::class);
    }

    public function createFill(Farmstead $farmstead): CriterionFill {
        return CriterionFill::create([
            'farmstead_id' => $farmstead->id,
            'criterion_id' => $this->id,
        ]);
    }


    public function evaluateFor(Farmstead $farmstead, EvaluationCache $cache = new EvaluationCache()): bool {
        return $cache->useCriterion($this, function($criterion) use ($farmstead, $cache) {
            /** @var CriterionFill $fill */
            $fill = $criterion->fills()->whereFarmsteadId($farmstead->id)->first();
            if ($fill == null) {
                $fill = CriterionFill::create(['farmstead_id' => $farmstead->id, 'criterion_id' => $criterion->id]);
            }
            $feval = $fill->evaluate($cache, $criterion);
            Log::debug('Result for criterion: ' . $criterion->id . " (" . ($feval ? 'true' : 'false')  . ")");
            return $feval;
        });
    }

    function evalLinkFor(Farmstead $farm, EvaluationCache $cache): bool {
        return $cache->useMilestoneId(intval($this->constant), function($milestone) use ($farm, $cache){
            return $milestone->evaluateFor($farm,$cache);
        });
    }
}
