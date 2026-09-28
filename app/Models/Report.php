<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'incident_date' => 'date',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class)->latest();
    }
}
