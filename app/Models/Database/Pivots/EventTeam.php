<?php

namespace App\Models\Database\Pivots;

use App\Models\Database\Enums\QualifierEnum;
use App\Models\Database\Event;
use App\Models\Database\Team;
use Database\Factories\EventTeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventTeam extends Pivot
{
    /** @use HasFactory<EventTeamFactory> */
    use HasFactory;

    protected $table = 'event_teams';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'team_id',
        'qualifier_id',
    ];

    protected static function newFactory(): EventTeamFactory
    {
        return EventTeamFactory::new();
    }

    protected function casts(): array
    {
        return [
            'qualifier_id' => QualifierEnum::class,
        ];
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
