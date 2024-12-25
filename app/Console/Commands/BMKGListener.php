<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BMKGListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:bmkg-weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch weather data from BMKG API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiUrl = config('services.bmkg.url', env('BMKG_API_URL', 'https://api.bmkg.go.id/publik/prakiraan-cuaca'));
        $adm4 = config('services.bmkg.key', env('BMKG_API_KEY'));

        $this->info("Fetching weather data for ADM4: {$adm4}");

        try {
            $response = Http::get($apiUrl, [
                'adm4' => $adm4,
            ]);

            if ($response->failed()) {
                $this->error('Failed to fetch weather data. Status: ' . $response->status());
                return Command::FAILURE;
            }

            $data = $response->json();

            $this->info('Weather data fetched successfully:');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
