<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecretaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'address'=>$this->address,
            'date_of_brith'=>$this->date_of_brith,
            'cv' => $this->cv ? asset('storage/' . $this->cv) : null,
            'doctor_id'=>$this->doctor_id,
            'image' =>$this->image?asset('storage/'.$this->image):null,

        ]
            ;
    }
}
