<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class appointment extends JsonResource
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
            'doctor_id'=>$this->doctor_id,
            'patient_id' => $this->patient_id,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'status' => $this->status,
            'reason_reservation' => $this->reason_reservation,

        ];
    }

}
