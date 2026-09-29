<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $guarded = ['id'];

    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'dosen');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'mahasiswa');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }
}
