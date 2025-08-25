<?php

namespace App\Models\Database;

use App\Http\Requests\EventRequest;
use App\Models\Database\Enums\CompetitorTypeEnum;
use App\Models\Database\Enums\EventStatusEnum;
use App\Models\Database\Enums\QualifierEnum;
use App\Models\Database\Pivots\EventTeam as EventTeamPivot;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use JsonException;

/**
 * @property-read Carbon|null $scheduled_at
 * @property-read EventStatusEnum|null $status_id
 * @property-read CompetitorTypeEnum|null $competitor_type_id
 * @property-read Team|null $homeTeam
 * @property-read Team|null $awayTeam
 * @property-read string $title
 */
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $table = 'events';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'league_id',
        'scheduled_at',
        'status_id',
        'competitor_type_id',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'slug' => 'string',
            'scheduled_at' => 'datetime',
            'status_id' => EventStatusEnum::class,
            'competitor_type_id' => CompetitorTypeEnum::class,
        ];
    }

    protected static function newFactory(): EventFactory
    {
        return EventFactory::new();
    }

    /**
     * @return HasMany<Market, $this>
     */
    public function markets(): HasMany
    {
        return $this->HasMany(Market::class, 'event_id', 'id');
    }

    /**
     * @return BelongsToMany<Team, $this, EventTeamPivot, 'pivot'>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'event_teams', 'event_id', 'team_id')
            ->using(EventTeamPivot::class)
            ->withPivot('qualifier_id');
    }

    /**
     * @return BelongsTo<League, $this>
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * @return Attribute<Team|null, never>
     */
    protected function homeTeam(): Attribute
    {
        return Attribute::make(
            get: function (): ?Team {
                if ($this->relationLoaded('teams')) {
                    $t = $this->teams->firstWhere('pivot.qualifier_id', QualifierEnum::HOME->value);

                    return $t instanceof Team ? $t : null; // ensure exact type
                }

                /** @var Team|null $t */
                $t = $this->teams()
                    ->wherePivot('qualifier_id', QualifierEnum::HOME->value)
                    ->first();

                return $t;
            }
        );

    }

    /**
     * @return Attribute<Team|null, never>
     */
    protected function awayTeam(): Attribute
    {
        return Attribute::make(
            get: function (): ?Team {
                if ($this->relationLoaded('teams')) {
                    $t = $this->teams->firstWhere('pivot.qualifier_id', QualifierEnum::AWAY->value);

                    return $t instanceof Team ? $t : null;
                }

                /** @var Team|null $t */
                $t = $this->teams()
                    ->wherePivot('qualifier_id', QualifierEnum::AWAY->value)
                    ->first();

                return $t;
            }
        );

    }

    /**
     * @return Attribute<string, never>
     */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => ucwords($this->name));
    }

    /**
     * @return LengthAwarePaginator<int, static>
     *
     * @phpstan-return LengthAwarePaginator<int, static>
     *
     * @throws JsonException
     */
    public function getEvents(EventRequest $request, string $searchTerm): LengthAwarePaginator
    {
        $perPage = 4;
        $page = (int) $request->input('page', 1);

        $cacheKey = 'events:index:'.md5(json_encode([
            'search' => $searchTerm,
            'starts_after' => $request->starts_after,
            'status_id' => $request->status_id,
            'page' => $page,
            'perPage' => $perPage,
        ], JSON_THROW_ON_ERROR));

        /** @var LengthAwarePaginator<int, static> */
        return Cache::remember($cacheKey, now()->addSeconds(1), static function () use ($request, $searchTerm, $perPage) {
            return static::query()
                ->with(['teams', 'league', 'markets', 'markets.selections'])
                ->when($searchTerm !== '', function (Builder $q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })
                ->when($request->starts_after, function (Builder $q, $starts_after) {
                    $q->whereDate('scheduled_at', '>=', $starts_after);
                })
                ->when($request->status_id, function (Builder $q, $status_id) {
                    $q->where('status_id', $status_id);
                })
                ->orderByDesc('scheduled_at')
                ->paginate($perPage)
                ->withQueryString();
        });
    }
}
