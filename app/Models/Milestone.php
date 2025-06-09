<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

}
