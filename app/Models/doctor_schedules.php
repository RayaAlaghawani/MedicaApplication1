<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class doctor_schedules extends Model
{
    protected $fillable = ['doctor_id', 'day_of_week', 'start_time', 'end_time', 'slot_duration'];
/////////
     protected $appends =['day_name'];
     /////
    public function getDayNameAttribute()
    {
        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        return isset($days[$this->day_of_week]) ? $days[$this->day_of_week] : 'يوم غير معروف';
    }
//////
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    protected $casts = [
        'day_of_week' => 'integer',
        'slot_duration' => 'integer',
    ];

}
