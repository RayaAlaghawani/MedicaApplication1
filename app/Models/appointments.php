<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class appointments extends Model
{
    use HasFactory;

    protected $guarded=[];


    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    protected $appends = ['created_at_formatted', 'updated_at_formatted'];

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->created_at)
            ->format('Y-m-d H:i'); // مثال: 2025-12-18 12:00
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return Carbon::parse($this->updated_at)
            ->format('Y-m-d H:i');
    }




}
