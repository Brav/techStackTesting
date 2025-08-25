<?php

namespace App\Http\Controllers;

use App\Http\Resources\SelectionResource;
use App\Models\Database\Selection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SelectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $selections = Selection::with('market', 'market.event')->get();

        return SelectionResource::collection($selections);
    }
}
