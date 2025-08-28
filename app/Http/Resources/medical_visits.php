<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class medical_visits extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'doctor_id'             => $this->doctor_id,
            'patient_id'            => $this->patient_id,
            'main_complaint'        => $this->main_complaint,
            'main_complaint_details'=> $this->main_complaint_details,
            'surgical_symptoms'     => $this->surgical_symptoms,
            'other_systems_review'  => $this->other_systems_review,
            'clinical_exam'         => $this->clinical_exam,
            'clinical_direction'    => $this->clinical_direction,
            'final_diagnosis'       => $this->final_diagnosis,
            'treatment'             => $this->treatment,
            'recommendations'       => $this->recommendations,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
