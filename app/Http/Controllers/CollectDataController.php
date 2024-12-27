<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectDataRequest;
use App\Http\Resources\GetAllCollectDataResources;
use App\Http\Resources\GetAllDataResources;
use App\Http\Resources\SendCollectDataResources;
use App\Models\AverageDaily;
use App\Models\CollectData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CollectDataController extends Controller
{

    public function SendCollectData(CollectDataRequest $request): SendCollectDataResources
    {
        // Validasi request untuk mengambil data yang diperlukan
        $validated = $request->validated();

        // Simpan data collect yang baru diterima
        $collectData = CollectData::create([
            'temperature' => $validated['temperature'],
            'air_humidity' => $validated['air_humidity'],
            'soil_humidity' => $validated['soil_humidity'],
        ]);

        // Panggil fungsi untuk menghitung rata-rata harian setelah data dikumpulkan
        $this->SaveDailyAverage($collectData->created_at);

        return new SendCollectDataResources($collectData);
    }

    public function SaveDailyAverage($timestamp)
    {
        // Ambil tanggal berdasarkan timestamp data yang baru saja disimpan
        $dateValue = $timestamp->toDateString(); // Mengambil tanggal dari timestamp (format Y-m-d)

        Log::info("Processing date: $dateValue");

        // Hitung rata-rata suhu, kelembapan udara, dan kelembapan tanah untuk tanggal tersebut
        $averages = CollectData::whereDate('created_at', $dateValue)
            ->select(
                DB::raw('AVG(temperature) as avg_temperature'),
                DB::raw('AVG(air_humidity) as avg_air_humidity'),
                DB::raw('AVG(soil_humidity) as avg_soil_humidity')
            )
            ->first();

        // Pastikan ada data yang ditemukan untuk dihitung rata-ratanya
        if ($averages) {
            // Mengecek apakah sudah ada data rata-rata untuk tanggal tersebut
            $existingAverage = AverageDaily::where('date', $dateValue)->first();

            if ($existingAverage) {
                // Jika ada, lakukan update pada entri yang sudah ada (hanya untuk hari yang sama)
                $existingAverage->update([
                    'avg_temperature' => $averages->avg_temperature,
                    'avg_air_humidity' => $averages->avg_air_humidity,
                    'avg_soil_humidity' => $averages->avg_soil_humidity,
                ]);
                Log::info("Average for $dateValue updated successfully.");
            } else {
                // Jika tidak ada, buat entri baru untuk hari tersebut
                AverageDaily::create([
                    'date' => $dateValue,
                    'avg_temperature' => $averages->avg_temperature,
                    'avg_air_humidity' => $averages->avg_air_humidity,
                    'avg_soil_humidity' => $averages->avg_soil_humidity,
                ]);

                // Log create
                Log::info("New average for $dateValue created successfully.");
            }
        } else {
            // Jika tidak ada data yang ditemukan untuk tanggal tersebut
            Log::warning("No data found for date $dateValue to calculate averages.");
        }
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
        $data = $query->get();

        return GetAllCollectDataResources::collection($data);
    }

    public function GetTodayCollectData()
    {
        $today = Carbon::today();
        $data = CollectData::whereDate('created_at', $today)->orderBy('created_at', 'desc')->get();

        if (!$data) {
            return response()->json([
                'error' => [
                    'status' => 'error',
                    'message' => 'No message data found for today.',
                ]
            ], 404);
        }
        return  GetAllCollectDataResources::collection($data);
    }

    public function GetLatestCollectData()
    {
        $data = CollectData::orderBy('created_at', 'desc')->first();
        if (!$data) {
            return response()->json([
                'error' => [
                    'status' => 'error',
                    'message' => 'No latest collect data data found.',
                ]
            ], 404);
        }
        return new GetAllCollectDataResources($data);
    }
}
