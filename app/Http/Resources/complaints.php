<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class complaints extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = class_basename($this->complaintable_type);

        // اسم مقدم الشكوى حسب نوعه
        if ($type ==='doctor') {
            $name = trim(($this->complaintable->first_name ?? '') . ' ' . ($this->complaintable->last_name ?? ''));
        } else {
            $name = $this->complaintable->name ?? null;
        }

        return [
            'id' => $this->id,
            'complaintable_type' => $this->complaintable_type,
            'complaintable_id' => $this->complaintable_id,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'admin_response' => $this->admin_response,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),

            'submitted_by' => [
                'name' => $name,
                'email' => $this->complaintable->email ?? null,
                'type' => $type,
                'phone' => $this->complaintable->phone ?? null,
            ],
        ];
    }
}
