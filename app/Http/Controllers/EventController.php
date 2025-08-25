<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Database\Event;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JsonException;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws JsonException
     */
    public function index(EventRequest $request): AnonymousResourceCollection
    {
        $searchTerm = str($request['search'] ?? '')
            ->trim()
            ->squish()
            ->toString();

        $eventModel = new Event;
        $events = $eventModel->getEvents($request, $searchTerm);

        return EventResource::collection($events);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): EventResource
    {
        $event->load(['teams', 'league', 'markets', 'markets.selections']);

        return new EventResource($event);
    }
}
