<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PastDiseasesTable extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'code' => $this->code,
            'diagnosed_at' => $this->diagnosed_at,
            'description' => $this->description,
            'doctor_id' => $this->doctor_id,
        ];
    }
}
