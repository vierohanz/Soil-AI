<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectDataRequest;
use App\Http\Resources\GetAllDataResources;
use App\Http\Resources\SendCollectDataResources;
use App\Models\AverageDaily;
use App\Models\CollectData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CollectDataController extends Controller
{

    public function SendCollectData(CollectDataRequest $request): SendCollectDataResources
    {
        $validated = $request->validated();

        $collectData = CollectData::create([
            'temperature' => $validated['temperature'],
            'air_humidity' => $validated['air_humidity'],
            'soil_humidity' => $validated['soil_humidity'],
            'light' => $validated['light'],
        ]);
        $this->SaveDailyAverage();

        return new SendCollectDataResources($collectData);
    }

    public function GetAllCollectData()
    {
        $query = CollectData::query();
        if ($id = request()->query('id')) {
            $query->where('id', $id);
        }
        if ($temperature = request()->query('temperature')) {
            $query->where('temperature', $temperature);
        }
        if ($air_humidity = request()->query('air_humidity')) {
            $query->where('air_humidity', $air_humidity);
        }
        if ($soil_humidity = request()->query('soil_humidity')) {
            $query->where('soil_humidity', $soil_humidity);
        }
        if ($light = request()->query('light')) {
            $query->where('light', $light);
        }
        $data = $query->get();

        return GetAllDataResources::collection($data);
    }

    public function SaveDailyAverage()
    {
        $dates = CollectData::selectRaw('DATE(created_at) as date')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->havingRaw('COUNT(*) >= 287')
            ->get();
        foreach ($dates as $date) {
            $dateValue = $date->date;

            Log::info("Processing date: $dateValue");
            if (!AverageDaily::where('date', $dateValue)->exists()) {
                $averages = CollectData::whereDate('created_at', $dateValue)
                    ->select(
                        DB::raw('AVG(temperature) as avg_temperature'),
                        DB::raw('AVG(air_humidity) as avg_air_humidity'),
                        DB::raw('AVG(soil_humidity) as avg_soil_humidity'),
                        DB::raw('AVG(light) as avg_light')
                    )
                    ->first();

                if ($averages) {
                    AverageDaily::create([
                        'date' => $dateValue,
                        'avg_temperature' => $averages->avg_temperature,
                        'avg_air_humidity' => $averages->avg_air_humidity,
                        'avg_soil_humidity' => $averages->avg_soil_humidity,
                        'avg_light' => $averages->avg_light,
                    ]);
                }
            }
        }
    }
}
