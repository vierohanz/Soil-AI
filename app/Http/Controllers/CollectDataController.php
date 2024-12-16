<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectDataRequest;
use App\Http\Resources\GetAllDataResources;
use App\Http\Resources\SendCollectDataResources;
use App\Models\CollectData;

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
}
