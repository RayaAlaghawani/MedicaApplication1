<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
        public function toArray(Request $request): array
    {
        $age = $this->patient->age;

        if ($age < 18) {
            return [
                'id'=> $this->id,
                'patient_type' => 'child',
                'residence' => $this->residence,
                'guardian_name' => $this->guardian_name,
                'guardian_phone' => $this->guardian_phone,
                'child_sleeps_well' => $this->child_sleeps_well,
                'has_chronic_disease' => $this->has_chronic_disease,
                'takes_medications' => $this->takes_medications,
                'has_allergies' => $this->has_allergies,
                'weight' => $this->weight,
                'height' => $this->height,
                'blood_type' => $this->blood_type,

            ];
        } else {
            return [
                'id'=> $this->id,
                'patient_type' => 'adult',
                'residence' => $this->residence,
                'phone_number' => $this->phone_number,
                'marital_status' => $this->marital_status,
                'profession' => $this->profession,
                'education' => $this->education,
                'insomnia' => $this->insomnia,
                'has_chronic_disease' => $this->has_chronic_disease,
                'takes_medications' => $this->takes_medications,
                'has_allergies' => $this->has_allergies,
                'weight' => $this->weight,
                'height' => $this->height,
                'blood_type' => $this->blood_type,
                'diet_type' => $this->diet_type,
                'drinks_alcohol' => $this->drinks_alcohol,
                'physical_activity_level' => $this->physical_activity_level,
                'sleep_hours' => $this->sleep_hours,
                'is_smoker' => $this->is_smoker,


            ];
        }
    }
}
