<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\CollectData;

class MqttListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to MQTT messages and store data to the database';

    public function handle()
    {
        $host = env('MQTT_BROKER_HOST', '127.0.0.1');
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

            // Subscribe to topic
            $mqtt->subscribe('collect/data', function (string $topic, string $message) {
                echo "Received message: " . $message . PHP_EOL;

                $data = json_decode($message, true);

                if ($data) {
                    CollectData::create([
                        'temperature' => $data['temperature'],
                        'air_humidity' => $data['air_humidity'],
                        'soil_humidity' => $data['soil_humidity'],
                        'light' => $data['light'],
                    ]);
                    echo "Data saved to database: " . json_encode($data) . PHP_EOL;
                } else {
                    echo "Invalid message format: " . $message . PHP_EOL;
                }
            }, 0);

            // Start listening
            $mqtt->loop(true);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . PHP_EOL;
        }
    }
}
