<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class surgical_procedures extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function RecordMedicals(){
        return $this->belongsToMany(
            RecordMedical::class,
            'record_medical_surgical_procedures',
        );

    }

}
