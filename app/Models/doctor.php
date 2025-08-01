<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class doctor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $fillable = [
        'specialization_id',
        'first_name',
        'last_name',
        'device_token',
        'email',
        'phone',
        'password',
        'image',
        'DateOfBirth',
        'CurriculumVitae',
        'Nationality',
        'ClinicAddress',
        'ProfessionalAssociationPhoto',
        'CertificateCopy',
        'consultation_fee',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(joinRequest::class);
    }

    public function Doctor_schedules()
    {
        return $this->hasMany(doctor_schedules::class);
    }




    public function appointments()
    {
        return $this->hasMany(appointments::class);
    }

    public function favourites()
    {
        return $this->hasMany(favourite::class);
    }
    public function articles(){
        return  $this->hasMany('article');
    }



}
