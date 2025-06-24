<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastDiseasesTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'code',
        'diagnosed_at',
        'description',
    ];


}
