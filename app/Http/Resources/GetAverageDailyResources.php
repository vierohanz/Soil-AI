<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAverageDailyResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->date,
            'avg_temperature' => $this->avg_temperature,
            'avg_air_humidity' => $this->avg_air_humidity,
            'avg_soil_humidity' => $this->avg_soil_humidity,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d\TH:i:s') : '',
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d\TH:i:s') : ''
        ];
    }
}
