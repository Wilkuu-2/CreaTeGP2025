<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone_number
 * @property bool $show_email
 * @property bool $show_number
 * @property Point|null $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereShowEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereShowNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Farmstead whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Farmstead extends Model
{
    //
    protected $fillable = ['name','email','phone_number','show_email','show_number', 'location'];
    /**
     * @return HasOne<User,Farmstead>
     */
    public function user(): HasOne {
        return $this->hasOne(User::class);
    }

    public $casts = [
        'location' => Point::class,
    ];

    public function evaluateCurrentMilestoneFor(Team $team): ?Milestone {
        if ($this->user == null || !$team->hasUser($this->user)) {
            return null; // User not in team, skipping.
        }
        Log::debug("$$$$$$$$$$$$$$ Starting eval for " . $this->id);
        $milestones = $team->milestones()->orderBy('order', 'desc')->get();
        $cache = new EvaluationCache();
        $current = null;
        /** @var Milestone $milestone*/
        forEach ($milestones as $milestone) {
            $result = $cache->useMilestone($milestone, function($milestone) use ($cache) {
                return $milestone->evaluateFor($this, $cache);
            });

            if ($result && $milestone->do_map) {
                $current = $milestone;
            }
        }
        return $current;
    }

}
