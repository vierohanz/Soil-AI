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
    public function GetRangeAverageDaily(AverageDailyRequest $request)
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

    public function GetAllAverageDaily()
    {
        $query = AverageDaily::query();
        if ($id = request()->query('id')) {
            $query->where('id', $id);
        }
        if ($date = request()->query('date')) {
            $query->where('date', $date);
        }
        if ($avg_temperature = request()->query('avg_temperature')) {
            $query->where('avg_temperature', $avg_temperature);
        }
        if ($avg_air_humidity = request()->query('avg_air_humidity')) {
            $query->where('avg_air_humidity', $avg_air_humidity);
        }
        if ($soil_humidity = request()->query('soil_humidity')) {
            $query->where('soil_humidity', $soil_humidity);
        }
        $data = $query->get();
        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        return GetAverageDailyResources::collection($data);
    }
}
