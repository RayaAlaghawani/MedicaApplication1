<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'specialization_name'=>$this->specialization->name,
            'specialization_id' => $this->specialization_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'image' =>$this->image?asset('storage/'.$this->image):null,
            'DateOfBirth' => $this->DateOfBirth,
            'phone' => $this->phone,
            'CurriculumVitae' => $this->CurriculumVitae ? asset('storage/' . $this->CurriculumVitae) : null,
            'Nationality' => $this->Nationality,
            'ClinicAddress' => $this->ClinicAddress,
            'ProfessionalAssociationPhoto' =>$this->ProfessionalAssociationPhoto?asset('storage/'.$this->ProfessionalAssociationPhoto):null,
            'CertificateCopy' =>$this->CertificateCopy?asset('storage/'.$this->CertificateCopy):null,
            'consultation_fee' => $this->consultation_fee,
        ];
    }

}
