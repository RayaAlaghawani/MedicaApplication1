<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class article extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function doctor()
    {
        return $this->belongsTo(doctor::class);
    }
    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

}
