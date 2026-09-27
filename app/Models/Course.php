<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = ['id'];

    public function classes()
    {
        return $this->hasMany(CourseClass::class);
    }
}
