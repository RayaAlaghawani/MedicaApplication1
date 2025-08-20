<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
class secretary extends Model
{


    use HasApiTokens, HasFactory, Notifiable;


    protected $guarded=[];
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
    public function complaintss()
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }
    public function complaintsecretary()
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }

    public function doctor()
    {
        return $this->belongsTo(doctor::class);
    }

//
//
//    public function getImageUrlAttribute()
//    {
//        return $this->image ? Storage::url($this->image) : null;
//    }
//
//    public function getCvUrlAttribute()
//    {
//        return $this->cv ? Storage::url($this->cv) : null;
//    }

}

