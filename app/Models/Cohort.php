<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cohort extends Model
{
    protected $guarded = ['id'];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'mahasiswa');
    }

    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }
}
