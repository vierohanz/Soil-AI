<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MqttService;  // Pastikan service sudah di-import

class MqttController extends Controller
{
    protected $mqttService;

    // Dependency injection untuk MqttService
    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    // Method untuk publikasi data ke topik MQTT
    public function publish(Request $request)
    {
        // Ambil data dari request
        $data = $request->validate([
            'soil_humidity' => 'required|numeric',
            'temperature' => 'required|numeric',
            'air_humidity' => 'required|numeric',
            'light' => 'required|numeric',
        ]);

        // Membuat pesan dalam format JSON
        $message = json_encode([
            'soil_humidity' => $data['soil_humidity'],
            'temperature' => $data['temperature'],
            'air_humidity' => $data['air_humidity'],
            'light' => $data['light'],
        ]);
        try {
            $this->mqttService->publish($data['topic'], $message);
            return response()->json(['message' => 'Data berhasil dipublikasikan!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mempublikasikan data', 'error' => $e->getMessage()], 500);
        }
    }
    public function subscribe(Request $request)
    {
        $topic = $request->input('topic');
        try {
            $this->mqttService->subscribe($topic);
            return response()->json(['message' => 'Berhasil berlangganan topik: ' . $topic], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal berlangganan topik', 'error' => $e->getMessage()], 500);
        }
    }
}
