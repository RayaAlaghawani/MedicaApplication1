<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastDiseasesTable extends Model
{
    use HasFactory;
    protected $table = 'past_diseases'; // هنا الاسم الصحيح للجدول

    protected $fillable = [
        'name',
        'type',
        'code',
        'diagnosed_at',
        'description',
       'doctor_id'
    ];

public function RecordMedicals(){
    return $this->belongsToMany(
        RecordMedical::class,
        'record_medical_past_diseases',
    );

}
    public function doctor()
    {
        return $this->belongsTo(doctor::class);
    }

}
