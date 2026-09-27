<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Reservation;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display the room exploration view.
     */
    public function index(Request $request)
    {
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
            $query->where('floor', (int)$selectedFloorId);
        }

        $searchQuery = $request->query('q', '');
        if (!empty($searchQuery)) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('code', 'like', "%{$searchQuery}%");
            });
        }

        $now = now();
        $day = $now->locale('id')->isoFormat('dddd');

        $rooms = $query->get()->map(function($room) use ($now, $day) {
            $isScheduled = Schedule::where('room_id', $room->id)
                ->where('day', $day)
                ->whereTime('start_time', '<=', $now->format('H:i:s'))
                ->whereTime('end_time', '>=', $now->format('H:i:s'))
                ->exists();

            $isReserved = Reservation::where('room_id', $room->id)
                ->where('date', $now->format('Y-m-d'))
                ->where('status', 'approved')
                ->whereTime('start_time', '<=', $now->format('H:i:s'))
                ->whereTime('end_time', '>=', $now->format('H:i:s'))
                ->exists();

            $status = $isScheduled ? 'DIGUNAKAN' : ($isReserved ? 'RESERVED' : 'KOSONG');

            return [
                'id' => $room->id,
                'code' => $room->code,
                'name' => $room->name,
                'capacity' => $room->capacity,
                'type' => $room->type,
                'status' => $status,
                'floorId' => (string)($room->floor ?? 1),
                'floor' => 'Lantai ' . ($room->floor ?? 1),
            ];
        });

        return view('rooms.index', [
            'buildings' => $buildings->map(fn($b) => ['id' => $b->id, 'name' => $b->name]),
            'floors' => $floors,
            'rooms' => $rooms,
            'selectedBuildingId' => $selectedBuildingId,
            'selectedFloorId' => $selectedFloorId,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Display a specific room and its daily schedule.
     */
    public function show($id)
    {
        $room = Room::with('building')->findOrFail($id);
        
        $now = now();
        $day = $now->locale('id')->isoFormat('dddd');

        $isScheduled = Schedule::where('room_id', $room->id)
            ->where('day', $day)
            ->whereTime('start_time', '<=', $now->format('H:i:s'))
            ->whereTime('end_time', '>=', $now->format('H:i:s'))
            ->exists();

        $isReserved = Reservation::where('room_id', $room->id)
            ->where('date', $now->format('Y-m-d'))
            ->where('status', 'approved')
            ->whereTime('start_time', '<=', $now->format('H:i:s'))
            ->whereTime('end_time', '>=', $now->format('H:i:s'))
            ->exists();

        $status = $isScheduled ? 'DIGUNAKAN' : ($isReserved ? 'RESERVED' : 'KOSONG');

        $roomData = [
            'id' => $room->id,
            'code' => $room->code,
            'name' => $room->name,
            'capacity' => $room->capacity,
            'type' => $room->type,
            'status' => $status,
            'buildingName' => $room->building->name ?? 'Gedung TI',
            'floorLabel' => 'Lantai ' . ($room->floor ?? 1),
            'floorId' => (string)($room->floor ?? 1),
        ];

        // Fetch actual schedules for today
        $dbSchedules = Schedule::where('room_id', $room->id)
            ->where('day', $day)
            ->with(['courseClass.course', 'courseClass.lecturer'])
            ->orderBy('start_time')
            ->get();

        // Fetch approved reservations for today
        $dbReservations = Reservation::where('room_id', $room->id)
            ->where('date', $now->format('Y-m-d'))
            ->where('status', 'approved')
            ->with('user')
            ->orderBy('start_time')
            ->get();

        $schedules = [];

        if ($dbSchedules->isNotEmpty() || $dbReservations->isNotEmpty()) {
            foreach ($dbSchedules as $s) {
                $schedules[] = [
                    'time' => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5),
                    'course' => $s->courseClass->course->name ?? 'Mata Kuliah',
                    'lecturer' => $s->courseClass->lecturer->name ?? '-',
                    'program' => 'TI',
                    'class' => $s->courseClass->name ?? '5A',
                    'status' => 'OCCUPIED'
                ];
            }
            foreach ($dbReservations as $r) {
                $schedules[] = [
                    'time' => substr($r->start_time, 0, 5) . ' - ' . substr($r->end_time, 0, 5),
                    'course' => 'Reservasi: ' . $r->purpose,
                    'lecturer' => $r->user->name ?? 'User',
                    'program' => 'Mahasiswa / Dosen',
                    'class' => '',
                    'status' => 'RESERVED_SLOT'
                ];
            }
        }

        if (empty($schedules)) {
            $schedules = [
                ['time' => '08:00 - 10:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
                ['time' => '10:00 - 12:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
                ['time' => '13:00 - 15:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
                ['time' => '15:00 - 17:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
            ];
        }

        return view('rooms.show', [
            'room' => $roomData,
            'schedules' => $schedules,
        ]);
    }
}
