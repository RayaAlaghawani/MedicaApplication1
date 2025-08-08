<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordMedical extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function PastDiseasesTable(){
    return $this->belongsToMany(
        PastDiseasesTable::class,
        'record_medical_past_diseases',
        'record_medical_id',
        'past_disease_id',
    );
}
    public function Medications(){
        return $this->belongsToMany(
            medications::class,
            'record_medical_medications',
        );
    }
////////////
    public function allergies()
    {
        return $this->hasMany(allergies::class);
    }
////////
    public function Examinationss()
    {
        return $this->hasMany(Examinations::class);
    }
    public function surgical_proceduress(){
        return $this->belongsToMany(
            surgical_procedures::class,
            'record_medical_surgical_procedures',
        );

    }

}
