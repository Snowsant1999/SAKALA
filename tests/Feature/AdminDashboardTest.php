<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_real_conflicts_and_report_priorities(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $building = Building::create(['code' => 'BLD-TEST', 'name' => 'Gedung Uji']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-TEST',
            'name' => 'Ruang Uji',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);

        Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => today(),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Praktikum A',
            'status' => 'pending',
        ]);
        Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => today(),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'purpose' => 'Praktikum B',
            'status' => 'pending',
        ]);
        Report::create([
            'reporter_id' => $student->id,
            'category' => 'Intimidasi',
            'incident_date' => today(),
            'location' => 'Gedung Uji',
            'description' => 'Laporan uji prioritas darurat.',
            'status' => 'pending',
            'priority' => 'urgent',
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('data-dashboard-live', false)
            ->assertSee('1 Konflik Terdeteksi')
            ->assertSee('Laporan Darurat')
            ->assertSee('Urgent')
            ->assertSee('Laporan Kampus Aman')
            ->assertSee('/admin/reservations?tab=conflicts');
    }
}
