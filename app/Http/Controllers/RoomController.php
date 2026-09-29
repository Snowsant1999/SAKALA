<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    private const AVAILABLE_WINDOWS = [
        ['start' => '08:00', 'end' => '10:00'],
        ['start' => '10:00', 'end' => '12:00'],
        ['start' => '13:00', 'end' => '15:00'],
        ['start' => '15:00', 'end' => '17:00'],
    ];

    /**
     * Display the room exploration view.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);
        $selectedDate = Carbon::parse($validated['date'] ?? today()->toDateString());

        $buildings = Building::with('rooms')->get();
        $selectedBuildingId = $request->query('building_id', $buildings->first()->id ?? null);

        $floors = [
            ['id' => 'all', 'label' => 'Semua Lantai'],
            ['id' => '1', 'label' => 'Lantai 1'],
            ['id' => '2', 'label' => 'Lantai 2'],
            ['id' => '3', 'label' => 'Lantai 3'],
            ['id' => '4', 'label' => 'Lantai 4'],
        ];
        $selectedFloorId = $request->query('floor_id', 'all');

        $query = Room::query();
        if ($selectedBuildingId) {
            $query->where('building_id', $selectedBuildingId);
        }

        if ($selectedFloorId && $selectedFloorId !== 'all') {
            $query->where('floor', (int) $selectedFloorId);
        }

        $searchQuery = $request->query('q', '');
        if (! empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('code', 'like', "%{$searchQuery}%");
            });
        }

        $rooms = $this->availabilityQuery($query, $selectedDate)
            ->get()
            ->map(function (Room $room) use ($selectedDate) {
                $availability = $this->buildAvailability($room);

                return [
                    'id' => $room->id,
                    'code' => $room->code,
                    'name' => $room->name,
                    'capacity' => $room->capacity,
                    'type' => $room->type,
                    'status' => $availability['roomStatus'],
                    'availableCount' => $availability['availableCount'],
                    'floorId' => (string) ($room->floor ?? 1),
                    'floor' => 'Lantai '.($room->floor ?? 1),
                    'date' => $selectedDate->toDateString(),
                ];
            });

        return view('rooms.index', [
            'buildings' => $buildings->map(fn ($b) => ['id' => $b->id, 'name' => $b->name]),
            'floors' => $floors,
            'rooms' => $rooms,
            'selectedBuildingId' => $selectedBuildingId,
            'selectedFloorId' => $selectedFloorId,
            'searchQuery' => $searchQuery,
            'selectedDate' => $selectedDate->toDateString(),
        ]);
    }

    /**
     * Display a specific room and its daily schedule.
     */
    public function show(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);
        $selectedDate = Carbon::parse($validated['date'] ?? today()->toDateString());
        $room = $this->availabilityQuery(Room::query()->with('building'), $selectedDate)
            ->findOrFail($id);
        $availability = $this->buildAvailability($room);

        $roomData = [
            'id' => $room->id,
            'code' => $room->code,
            'name' => $room->name,
            'capacity' => $room->capacity,
            'type' => $room->type,
            'status' => $availability['roomStatus'],
            'buildingName' => $room->building->name ?? 'Gedung TI',
            'floorLabel' => 'Lantai '.($room->floor ?? 1),
            'floorId' => (string) ($room->floor ?? 1),
        ];

        return view('rooms.show', [
            'room' => $roomData,
            'schedules' => $availability['schedules'],
            'selectedDate' => $selectedDate->toDateString(),
        ]);
    }

    private function availabilityQuery(Builder $query, Carbon $selectedDate): Builder
    {
        $day = $selectedDate->locale('id')->isoFormat('dddd');

        return $query->with([
            'schedules' => fn ($scheduleQuery) => $scheduleQuery
                ->where('day', $day)
                ->with(['courseClass.course', 'courseClass.lecturer']),
            'reservations' => fn ($reservationQuery) => $reservationQuery
                ->whereDate('date', $selectedDate->toDateString())
                ->where('status', 'approved')
                ->with('user'),
        ]);
    }

    private function buildAvailability(Room $room): array
    {
        $events = [];

        foreach ($room->schedules as $schedule) {
            if (in_array(strtoupper($schedule->mode), ['ONLINE', 'CANCELLED'], true)) {
                continue;
            }

            $events[] = [
                'start' => substr($schedule->start_time, 0, 5),
                'end' => substr($schedule->end_time, 0, 5),
                'status' => 'OCCUPIED',
                'course' => $schedule->courseClass?->course?->name ?? 'Mata Kuliah',
                'lecturer' => $schedule->courseClass?->lecturer?->name ?? '-',
                'program' => 'TI',
                'class' => $schedule->courseClass?->name ?? '5A',
            ];
        }

        foreach ($room->reservations as $reservation) {
            $events[] = [
                'start' => substr($reservation->start_time, 0, 5),
                'end' => substr($reservation->end_time, 0, 5),
                'status' => 'RESERVED_SLOT',
                'course' => 'Reservasi: '.$reservation->purpose,
                'lecturer' => $reservation->user?->name ?? 'User',
                'program' => 'Mahasiswa / Dosen',
                'class' => '',
            ];
        }

        $boundaries = ['08:00', '10:00', '12:00', '13:00', '15:00', '17:00'];
        foreach ($events as $event) {
            $boundaries[] = $event['start'];
            $boundaries[] = $event['end'];
        }
        $boundaries = array_values(array_unique($boundaries));
        sort($boundaries);

        $schedules = [];
        for ($index = 0; $index < count($boundaries) - 1; $index++) {
            $start = $boundaries[$index];
            $end = $boundaries[$index + 1];
            $matchingEvent = collect($events)->first(
                fn (array $event): bool => $event['start'] <= $start && $event['end'] >= $end,
            );
            $isAvailableWindow = collect(self::AVAILABLE_WINDOWS)->contains(
                fn (array $window): bool => $window['start'] <= $start && $window['end'] >= $end,
            );

            if (! $matchingEvent && ! $isAvailableWindow) {
                continue;
            }

            $schedules[] = [
                'time' => $start.' - '.$end,
                'course' => $matchingEvent['course'] ?? 'Slot tersedia',
                'lecturer' => $matchingEvent['lecturer'] ?? '-',
                'program' => $matchingEvent['program'] ?? '-',
                'class' => $matchingEvent['class'] ?? '',
                'status' => $matchingEvent['status'] ?? 'AVAILABLE',
            ];
        }

        $availableCount = collect($schedules)->where('status', 'AVAILABLE')->count();
        $hasReservation = collect($schedules)->contains('status', 'RESERVED_SLOT');
        $hasClass = collect($schedules)->contains('status', 'OCCUPIED');
        $roomStatus = $availableCount > 0
            ? 'KOSONG'
            : ($hasReservation ? 'RESERVED' : ($hasClass ? 'PENUH' : 'KOSONG'));

        return [
            'roomStatus' => $roomStatus,
            'availableCount' => $availableCount,
            'schedules' => $schedules,
        ];
    }
}
