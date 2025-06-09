<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\CType;
use App\Enums\DType;
use App\Enums\Operator;

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
}
