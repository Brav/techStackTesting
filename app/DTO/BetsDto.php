<?php

namespace App\DTO;

use App\Http\Requests\BetsRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\SelectionResource;
use App\Models\Database\Selection;
use App\Service\BetCalculatorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BetsDto
{
    /**
     * @return array{
     *     stake: float,
     *     combine_odds: float,
     *     potential_payout: float,
     *     selections: AnonymousResourceCollection,
     *     event: EventResource }
     */
    public static function betResponse(BetsRequest $request): array
    {

        $selectionModel = new Selection;

        /** @var Collection<int, Selection> $selections */
        $selections = $selectionModel->getSelections($request->selection_ids);

        $combineOdds = BetCalculatorService::combineOdds($selections);

        $potentialPayout = BetCalculatorService::potentialPayout($combineOdds, $request->stake);

        return [
            'stake' => $request->stake,
            'combine_odds' => $combineOdds,
            'potential_payout' => $potentialPayout,
            'selections' => SelectionResource::collection($selections),
            'event' => EventResource::make($selections->first()->market->event),
        ];
    }
}
