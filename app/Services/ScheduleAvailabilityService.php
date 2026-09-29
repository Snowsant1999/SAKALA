<?php

namespace App\Services;

use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleAvailabilityService
{
    public function conflicts(int $roomId, string $date, string $startTime, string $endTime): bool
    {
        $selectedDate = Carbon::parse($date);
        $day = $selectedDate->locale('id')->isoFormat('dddd');

        return Schedule::query()
            ->where('room_id', $roomId)
            ->where('day', $day)
            ->where('mode', 'ONSITE')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->whereDoesntHave('exceptions', fn ($query) => $query->whereDate('date', $selectedDate->toDateString()))
            ->exists();
    }
}
