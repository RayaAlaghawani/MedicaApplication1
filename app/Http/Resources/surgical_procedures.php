<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class surgical_procedures extends JsonResource
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
            'doctor_id' => $this->doctor_id,
            'record_medical_id' => $this->record_medical_id,
            'name' => $this->name,
            'type' => $this->type,
            'procedure_date' => $this->procedure_date,
        ];
    }
}
