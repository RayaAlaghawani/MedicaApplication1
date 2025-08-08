<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class article extends JsonResource
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
            'specialization_id' => $this->specialization_id,
            'doctor_id' => $this->doctor_id,
            'title' => $this->title,
            'specialization_name' => optional($this->specialization)->name,
            'doctor_first_name' => optional($this->doctor)->first_name,
            'doctor_last_name' => optional($this->doctor)->last_name,
            'published_at' => $this->published_at,
            'status' => $this->status,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'content' => $this->content,
            'category' => $this->category,
            'summary' => $this->summary,
        ];
    }}
