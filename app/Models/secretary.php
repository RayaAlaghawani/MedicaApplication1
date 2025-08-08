<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class secretary extends Model
{
    use HasFactory;
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

}

