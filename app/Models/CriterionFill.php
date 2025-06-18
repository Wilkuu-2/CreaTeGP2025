<?php

namespace App\Models;

use App\Enums\Operator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 *
 *
 * @property int $criterion_id
 * @property bool|null $bool1
 * @property bool|null $bool2
 * @property int|null $int1
 * @property int|null $int2
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CriterionFillFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereBool1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereBool2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereCriterionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereInt1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereInt2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereUpdatedAt($value)
 * @property int $farmstead_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereFarmsteadId($value)
 * @property float|null $double1
 * @property float|null $double2
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereDouble1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CriterionFill whereDouble2($value)
 * @mixin \Eloquent
 */
class CriterionFill extends Model
{
    /** @use HasFactory<\Database\Factories\CriterionFillFactory> */
    use HasFactory;
    protected $primaryKey = ['farmstead_id', 'criterion_id'];
    public $incrementing = false;
    protected $fillable = ['farmstead_id','criterion_id','bool1', 'bool2', 'int1', 'int2', 'double1', 'double2' , 'notes'];

    /**
     * @return BelongsTo<Criterion,CriterionFill>
     */
    public function criterion(): BelongsTo {
        return $this->belongsTo(Criterion::class);
    }
    public function farmstead(): BelongsTo {
        return $this->belongsTo(Farmstead::class);
    }

    public function evaluate(EvaluationCache $cache, ?Criterion $criterion): bool {
        $crit = $criterion ?: $this->criterion;
        // TODO: Cycle detection
        // Log::debug($this->bool1);
        switch($crit->operator){
            case Operator::GTE:
				return $this->double1 != null && $this->double1 >= floatval($crit->constant);
            case Operator::GT:
				return $this->double1 != null && $this->double1 > floatval($crit->constant);
            case Operator::LTE:
				return $this->double1 <= floatval($crit->constant);
            case Operator::LT:
				return $this->double1 < floatval($crit->constant);
            case Operator::LINK:
				return $crit->evalLinkFor($this->farmstead, $cache);
            case Operator::CHECK:
                Log::debug("== " . $crit->id);
                Log::debug("bool:" . intval($this->bool1));
                Log::debug("constant:" . $crit->constant);
                Log::debug($crit->operator->value);
				return $this->bool1 != null && intval($this->bool1) == intval($crit->constant);
            default:
                abort(500, 'invalid operation for criterion: ' . $crit->operator->value);
        };
    }
    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}

