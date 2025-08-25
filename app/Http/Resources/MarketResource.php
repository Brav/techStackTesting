<?php

namespace App\Http\Resources;

use App\Models\Database\Market;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Market */
class MarketResource extends JsonResource
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
            'event' => EventResource::make($this->whenLoaded('event')),
            'selections' => SelectionResource::collection($this->whenLoaded('selections')),
        ];
    }
}
