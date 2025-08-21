<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class notification extends Model
{
    use HasFactory;
    public function notifiable():MorphTo
    {
        return $this->morphTo();
    }

}
