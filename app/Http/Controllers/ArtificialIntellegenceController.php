<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArtificialIntellegenceRequest;
use App\Http\Resources\ArtificialIntellegenceResources;
use App\Http\Resources\GetAllMessageData;
use App\Http\Resources\GetMessageDataResources;
use App\Http\Resources\SendMessageResources;
use App\Models\ArtificialIntellegence;
use App\Models\AverageDaily;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtificialIntellegenceController extends Controller
{
    public function SendMessage(ArtificialIntellegenceRequest $request): JsonResponse|SendMessageResources
    {
        $validated = $request->validated();
        $today = now()->toDateString();
        $averageDaily = AverageDaily::whereDate('date', $today)->first();

        if (!$averageDaily) {
            return response()->json([
                'status' => 'error',
                'message' => 'No average data found for today.',
            ], 404);
        }
        $artificialIntellegence = ArtificialIntellegence::updateOrCreate(
            [
                'average_id' => $averageDaily->id,
            ],
            [
                'message' => $validated['message'],
            ]
        );

        return new SendMessageResources($artificialIntellegence);
    }

    public function GetMessageData()
    {
        $query = ArtificialIntellegence::query();
        if ($id = request()->query('id')) {
            $query->where('id', $id);
        }
        if ($message = request()->query('message')) {
            $query->where('message', $message);
        }
        if ($average_id = request()->query('average_id')) {
            $query->where('average_id', $average_id);
        }
        $data = $query->get();

        return GetMessageDataResources::collection($data);
    }
}
