<?php

namespace App\Http\Resources;

use App\Models\Database\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @extends JsonResource<Event> */
/** @mixin Event */
class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'league' => LeagueResource::make($this->whenLoaded('league')),
            'markets' => MarketResource::collection($this->whenLoaded('markets')),
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status_id?->value,
            'home_team' => $this->when(
                $this->relationLoaded('teams'),
                fn () => TeamResource::make($this->home_team)
            ),
            'away_team' => $this->when(
                $this->relationLoaded('teams'),
                fn () => TeamResource::make($this->away_team)
            ),
        ];

    }
}
