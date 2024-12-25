<?php

namespace App\Console\Commands;

use App\Models\AverageDaily;
use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\CollectData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MqttListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to MQTT messages and store data to the database';

    public function handle()
    {
        $host = env('MQTT_BROKER_HOST', 'soilapi.hcorp.my.id');
        $port = env('MQTT_BROKER_PORT', 1883);
        $clientId = env('MQTT_CLIENT_ID', 'soilai17');

        $connectionSettings = (new ConnectionSettings)
            ->setUsername(env('MQTT_BROKER_USERNAME'))
            ->setPassword(env('MQTT_BROKER_PASSWORD'))
            ->setKeepAliveInterval(60)
            ->setUseTls(false);

        try {
            $mqtt = new MqttClient($host, $port, $clientId);
            $mqtt->connect($connectionSettings);
            echo "Connected to MQTT Broker!" . PHP_EOL;

            $mqtt->subscribe('collect/data', function (string $topic, string $message) {
                echo "Received message: " . $message . PHP_EOL;

                $data = json_decode($message, true);

                if ($data) {
                    CollectData::create([
                        'temperature' => $data['temperature'],
                        'air_humidity' => $data['air_humidity'],
                        'soil_humidity' => $data['soil_humidity'],
                    ]);
                    echo "Data saved to database: " . json_encode($data) . PHP_EOL;
                    $this->SaveDailyAverage();
                } else {
                    echo "Invalid message format: " . $message . PHP_EOL;
                }
            }, 0);

            $mqtt->loop(true);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . PHP_EOL;
        }
    }

    public function SaveDailyAverage()
    {
        $dates = CollectData::selectRaw('DATE(created_at) as date')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->havingRaw('COUNT(*) >= 2')
            ->get();

        foreach ($dates as $date) {
            $dateValue = $date->date;

            Log::info("Processing date: $dateValue");

            // Cek apakah sudah ada data rata-rata untuk tanggal ini
            if (!AverageDaily::where('date', $dateValue)->exists()) {
                $averages = CollectData::whereDate('created_at', $dateValue)
                    ->select(
                        DB::raw('AVG(temperature) as avg_temperature'),
                        DB::raw('AVG(air_humidity) as avg_air_humidity'),
                        DB::raw('AVG(soil_humidity) as avg_soil_humidity')
                    )
                    ->first();

                if ($averages) {
                    AverageDaily::create([
                        'date' => $dateValue,
                        'avg_temperature' => $averages->avg_temperature,
                        'avg_air_humidity' => $averages->avg_air_humidity,
                        'avg_soil_humidity' => $averages->avg_soil_humidity
                    ]);

                    Log::info("Average data created for date: $dateValue");
                }
            }
        }
    }
}
