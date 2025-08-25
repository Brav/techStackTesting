<?php

namespace App\Http\Controllers;

use App\DTO\BetsDto;
use App\Http\Requests\BetsRequest;
use Illuminate\Http\JsonResponse;

class BetsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(BetsRequest $request): JsonResponse
    {
        $bets = BetsDto::betResponse($request);

        return response()->json($bets);
    }
}
