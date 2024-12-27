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
            ->setUseTls(false); // Update based on your broker's security settings

        try {
            $mqtt = new MqttClient($host, $port, $clientId);
            $mqtt->connect($connectionSettings);
            echo "Connected to MQTT Broker!" . PHP_EOL;

            $mqtt->subscribe('collect/data', function (string $topic, string $message) use ($mqtt) {
                try {
                    $data = json_decode($message, true);

                    if ($data) {
                        $collectData = CollectData::create([
                            'temperature' => $data['temperature'],
                            'air_humidity' => $data['air_humidity'],
                            'soil_humidity' => $data['soil_humidity'],
                        ]);

                        $this->saveDailyAverage($collectData->created_at);
                        echo "Data saved to database: " . json_encode($data) . PHP_EOL;
                    } else {
                        Log::warning("Invalid message format: " . $message);
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing message: " . $e->getMessage());
                }
            }, 0);

            $mqtt->loop(true);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . PHP_EOL;
            Log::error("Error connecting to MQTT broker: " . $e->getMessage());
        }
    }

    private function saveDailyAverage(Carbon $timestamp)
    {
        $dateValue = $timestamp->toDateString();
        Log::info("Processing date: $dateValue");

        $averages = CollectData::whereDate('created_at', $dateValue)
            ->select(
                DB::raw('AVG(temperature) as avg_temperature'),
                DB::raw('AVG(air_humidity) as avg_air_humidity'),
                DB::raw('AVG(soil_humidity) as avg_soil_humidity')
            )
            ->first();

        if ($averages) {
            $existingAverage = AverageDaily::where('date', $dateValue)->first();

            if ($existingAverage) {
                $existingAverage->update([
                    'avg_temperature' => $averages->avg_temperature,
                    'avg_air_humidity' => $averages->avg_air_humidity,
                    'avg_soil_humidity' => $averages->avg_soil_humidity,
                ]);
                Log::info("Average for $dateValue updated successfully.");
            } else {
                AverageDaily::create([
                    'date' => $dateValue,
                    'avg_temperature' => $averages->avg_temperature,
                    'avg_air_humidity' => $averages->avg_air_humidity,
                    'avg_soil_humidity' => $averages->avg_soil_humidity,
                ]);
                Log::info("New average for $dateValue created successfully.");
            }
        } else {
            Log::warning("No data found for date $dateValue to calculate averages.");
        }
    }
}
