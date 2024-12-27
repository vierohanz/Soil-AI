<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetMessageDataResources extends JsonResource
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
            'message' => $this->message,
            'prob_tidak_siram' => $this->prob_tidak_siram,
            'prob_siram' => $this->prob_siram,
            'average_id' => $this->average_id,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d\TH:i:s') : '',
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d\TH:i:s') : ''
        ];
    }
}
