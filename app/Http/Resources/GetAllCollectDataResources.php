<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAllCollectDataResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'temperature' => $this->temperature,
            'air_humidity' => $this->air_humidity,
            'soil_humidity' => $this->soil_humidity,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d\TH:i:s') : '',
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d\TH:i:s') : ''
        ];
    }
}
