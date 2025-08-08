<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class Patient extends Model
{
    use  HasApiTokens,HasFactory, Notifiable;

    protected $guarded = [];
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'email_verified',
        'email_verification_code',
        'age',
        'profile_image',
    ];
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Added: For automatic password hashing
    ];
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        return null;
    }


    public function medicalRecord()
    {
        return $this->hasOne(RecordMedical::class, 'patient_id');
    }


    public function appointments()
    {
        return $this->hasMany(appointments::class);
    }




    public function favourites()
    {
        return $this->hasMany(favourite::class);
    }


//معاينات
    public function MedicalVisits()
    {
        return $this->hasMany(medical_visits::class);
    }
    public function doctors()
    {
        return $this->belongsToMany(doctor::class,'medical_visits');
    }

    public function doctor_appointments()
    {
        return $this->belongsToMany(doctor::class,'appointments');
    }
    public function complaintpatient()
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }

}
