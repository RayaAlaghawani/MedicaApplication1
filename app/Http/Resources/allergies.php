<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class allergies extends JsonResource
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
            'allergy_type' => $this->allergy_type,
            'allergen' => $this->allergen,
            'severity' => $this->severity,
            'is_private' => (bool) $this->is_private, // cast to boolean
            'doctor_id' => $this->doctor_id,
            'record_medical_id' => $this->record_medical_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
