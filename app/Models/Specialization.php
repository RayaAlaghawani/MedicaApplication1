<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class specialization extends Model
{
    use HasFactory;
    use  HasApiTokens,HasFactory;
    protected $guarded=[];
        // تعريف العلاقة مع جدول الأطباء//////1
        public function doctors()
        {
            return $this->hasMany(doctor::class);
        }
  ////////////////////!!2


}
