<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = ['id'];

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
