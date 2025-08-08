<?php

namespace App\Models;
use App\Models\Patient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Complaint extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function complaintable():MorphTo
    {
        return $this->morphTo();
    }
    public $timestamps = true;

}
