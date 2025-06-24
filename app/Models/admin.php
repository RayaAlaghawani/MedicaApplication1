<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class admin extends Authenticatable
{
    use  HasApiTokens,HasFactory;
    protected $guarded=[];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [

        'password' => 'hashed',
    ];
}
