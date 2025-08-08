<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class medications extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function RecordMedicals(){
        return $this->belongsToMany(
            RecordMedical::class,
            'record_medical_medications',
        );

}}
