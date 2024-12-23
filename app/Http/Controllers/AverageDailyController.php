<?php

namespace App\Http\Controllers;

use App\Http\Requests\AverageDailyRequest;
use App\Http\Resources\AverageDailyResources;
use App\Http\Resources\GetAverageDailyResources;
use App\Models\AverageDaily;
use App\Models\CollectData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AverageDailyController extends Controller
{
    public function GetAverageDaily(AverageDailyRequest $request)
    {
        $validated = $request->validated();
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $averageData = AverageDaily::whereBetween('date', [$startDate, $endDate])->get();

        if ($averageData->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No average data found for the specified date range.',
            ], 404);
        }

        return GetAverageDailyResources::collection($averageData);
    }

    
}
