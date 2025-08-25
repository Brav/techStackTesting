<?php

namespace App\Models\Database;

use App\Models\Database\Pivots\EventTeam as EventTeamPivot;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $title
 */
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }

    /** @return HasMany<EventTeamPivot, $this> */
    public function eventTeams(): HasMany
    {
        return $this->hasMany(EventTeamPivot::class, 'team_id');
    }

    /**
     * @return BelongsToMany<Event, $this, EventTeamPivot, 'pivot'>
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_teams', 'team_id', 'event_id')
            ->using(EventTeamPivot::class)
            ->withPivot('qualifier_id');
    }

    /**
     * @return Attribute<string, never>
     */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => ucwords($this->name));
    }
}
