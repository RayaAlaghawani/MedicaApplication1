<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examinations extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'type',
        'image_path',
        'exam_date',
        'summary',
    ];



}
