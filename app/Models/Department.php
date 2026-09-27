<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = ['id'];

    public function studyPrograms()
    {
        return $this->hasMany(StudyProgram::class);
    }

    public function lecturers()
    {
        return $this->hasMany(User::class)->where('role', 'dosen');
    }
}
