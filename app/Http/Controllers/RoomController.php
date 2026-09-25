<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Mock data for buildings, floors, and rooms.
     */
    private array $mockBuildings = [
        ['id' => 'bld-01', 'name' => 'Gedung Teknologi Informasi'],
        ['id' => 'bld-02', 'name' => 'Gedung Teknik Elektro'],
    ];

    private array $mockFloors = [
        'bld-01' => [
            ['id' => 'flr-01-1', 'level' => 1, 'label' => 'Lantai 1'],
            ['id' => 'flr-01-2', 'level' => 2, 'label' => 'Lantai 2'],
            ['id' => 'flr-01-3', 'level' => 3, 'label' => 'Lantai 3'],
            ['id' => 'flr-01-4', 'level' => 4, 'label' => 'Lantai 4'],
        ],
        'bld-02' => [
            ['id' => 'flr-02-1', 'level' => 1, 'label' => 'Lantai 1'],
            ['id' => 'flr-02-2', 'level' => 2, 'label' => 'Lantai 2'],
        ]
    ];

    private array $mockRooms = [
        // Gedung TI, Lantai 1
        ['id' => 'rm-001', 'code' => 'TI-101', 'name' => 'Kelas 1', 'floorId' => 'flr-01-1', 'capacity' => 30, 'type' => 'Kelas', 'status' => 'DIGUNAKAN'],
        ['id' => 'rm-002', 'code' => 'TI-102', 'name' => 'Lab Multimedia', 'floorId' => 'flr-01-1', 'capacity' => 30, 'type' => 'Laboratorium', 'status' => 'KOSONG'],
        // Gedung TI, Lantai 2
        ['id' => 'rm-003', 'code' => 'TI-201', 'name' => 'Kelas 2A', 'floorId' => 'flr-01-2', 'capacity' => 40, 'type' => 'Kelas', 'status' => 'DIGUNAKAN'],
        ['id' => 'rm-004', 'code' => 'TI-202', 'name' => 'Lab Rekayasa Komputer', 'floorId' => 'flr-01-2', 'capacity' => 25, 'type' => 'Laboratorium', 'status' => 'RESERVED'],
        ['id' => 'rm-005', 'code' => 'TI-203', 'name' => 'Kelas 2B', 'floorId' => 'flr-01-2', 'capacity' => 30, 'type' => 'Kelas', 'status' => 'KOSONG'],
        // Gedung TI, Lantai 3
        ['id' => 'rm-006', 'code' => 'TI-301', 'name' => 'Aula Gedung TI', 'floorId' => 'flr-01-3', 'capacity' => 100, 'type' => 'Aula', 'status' => 'MAINTENANCE'],
        // Gedung Elektro, Lantai 1
        ['id' => 'rm-007', 'code' => 'TE-101', 'name' => 'Lab Kendali', 'floorId' => 'flr-02-1', 'capacity' => 20, 'type' => 'Laboratorium', 'status' => 'KOSONG'],
        ['id' => 'rm-008', 'code' => 'TE-102', 'name' => 'Kelas TE-1', 'floorId' => 'flr-02-1', 'capacity' => 30, 'type' => 'Kelas', 'status' => 'KOSONG'],
    ];

    /**
     * Display the room exploration view (Left wireframe).
     */
    public function index(Request $request)
    {
        $buildings = $this->mockBuildings;
        
        // Handle filter state
        $selectedBuildingId = $request->query('building_id', $buildings[0]['id']);
        $floors = $this->mockFloors[$selectedBuildingId] ?? [];
        $selectedFloorId = $request->query('floor_id', $floors[0]['id'] ?? null);

        // Filter rooms based on selected floor
        $rooms = array_filter($this->mockRooms, function($room) use ($selectedFloorId) {
            return $room['floorId'] == $selectedFloorId;
        });

        // Search query
        $searchQuery = $request->query('q', '');
        if (!empty($searchQuery)) {
            $rooms = array_filter($rooms, function($room) use ($searchQuery) {
                return stripos($room['name'], $searchQuery) !== false || stripos($room['code'], $searchQuery) !== false;
            });
        }

        return view('rooms.index', compact('buildings', 'floors', 'rooms', 'selectedBuildingId', 'selectedFloorId', 'searchQuery'));
    }

    /**
     * Display a specific room and its daily schedule (Right wireframe).
     */
    public function show($id)
    {
        // Find room
        $room = collect($this->mockRooms)->firstWhere('id', $id);
        
        if (!$room) {
            abort(404, 'Ruangan tidak ditemukan');
        }

        // Add extra details for the show view
        $room['buildingName'] = collect($this->mockBuildings)->firstWhere('id', collect($this->mockFloors)->flatMap(function ($floors, $bId) use ($room) {
            return collect($floors)->firstWhere('id', $room['floorId']) ? [$bId] : [];
        })->first())['name'] ?? 'Unknown Building';
        
        $room['floorLabel'] = collect($this->mockFloors)->flatten(1)->firstWhere('id', $room['floorId'])['label'] ?? 'Unknown Floor';

        // Mock schedule for this specific room today
        $schedules = [];
        
        if ($room['status'] == 'DIGUNAKAN') {
            $schedules = [
                ['time' => '09:00 - 12:00', 'course' => 'Blockchain', 'lecturer' => 'Sufarman', 'program' => 'TRK', 'class' => '5A', 'status' => 'OCCUPIED'],
                ['time' => '13:00 - 15:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
                ['time' => '15:00 - 17:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
            ];
        } elseif ($room['status'] == 'RESERVED') {
            $schedules = [
                ['time' => '08:00 - 10:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
                ['time' => '13:00 - 15:00', 'course' => 'Reservasi: Praktikum Jaringan', 'lecturer' => 'Andi Pratama (Mhs)', 'program' => 'TRK', 'class' => '5A', 'status' => 'RESERVED_SLOT'],
                ['time' => '15:00 - 17:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
            ];
        } elseif ($room['status'] == 'MAINTENANCE') {
             $schedules = [
                ['time' => '08:00 - 17:00', 'course' => 'Maintenance Rutin', 'status' => 'MAINTENANCE_SLOT'],
            ];
        } else {
             $schedules = [
                ['time' => '08:00 - 10:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
                ['time' => '10:00 - 12:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
                ['time' => '13:00 - 15:00', 'course' => 'Available Slot', 'status' => 'AVAILABLE'],
            ];
        }

        return view('rooms.show', compact('room', 'schedules'));
    }
}
