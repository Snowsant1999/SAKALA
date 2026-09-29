<?php

namespace App\Services;

use App\Models\Reservation;

class ReservationConflictDetector
{
    public function groups(iterable $reservations): array
    {
        $byRoomAndDate = collect($reservations)
            ->filter(fn (Reservation $reservation): bool => in_array($reservation->status, ['pending', 'approved'], true))
            ->groupBy(fn (Reservation $reservation): string => $reservation->room_id.'|'.$reservation->date->format('Y-m-d'));
        $conflicts = [];

        foreach ($byRoomAndDate as $group) {
            $ordered = $group->sortBy('start_time')->values();
            $overlapGroup = [];
            $groupEnd = null;

            foreach ($ordered as $reservation) {
                if ($overlapGroup === [] || $reservation->start_time < $groupEnd) {
                    $overlapGroup[] = $reservation;
                    $groupEnd = max($groupEnd ?? $reservation->end_time, $reservation->end_time);

                    continue;
                }

                if (count($overlapGroup) > 1) {
                    $conflicts[] = $overlapGroup;
                }

                $overlapGroup = [$reservation];
                $groupEnd = $reservation->end_time;
            }

            if (count($overlapGroup) > 1) {
                $conflicts[] = $overlapGroup;
            }
        }

        return $conflicts;
    }
}
