<?php

namespace App\Http\Controllers;

use App\Models\Database\Enums\EventStatusEnum;
use Illuminate\Http\JsonResponse;

class EnumController extends Controller
{
    public function eventStatuses(): JsonResponse
    {

        $data = array_map(static function (EventStatusEnum $e): array {
            return [
                'name' => $e->name,
                'value' => $e->value,
                'label' => ucwords(strtolower(str_replace('_', ' ', $e->name))),
            ];
        }, EventStatusEnum::cases());

        return response()->json(['data' => $data]);
    }
}
