<?php

namespace App\Http\Resources;

use App\Models\Database\Selection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Selection */
class SelectionResource extends JsonResource
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
            'market' => MarketResource::make($this->whenLoaded('market')),
            'odds' => $this->odds,
        ];
    }
}
