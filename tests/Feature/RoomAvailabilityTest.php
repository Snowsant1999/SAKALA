<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_detail_uses_the_selected_date_and_shows_approved_and_available_slots(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $building = Building::create(['code' => 'BLD-ROOM-TEST', 'name' => 'Gedung Uji']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-AVAILABILITY',
            'name' => 'Ruang Ketersediaan',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        $selectedDate = today()->addDays(2)->format('Y-m-d');

        $reservation = Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => $selectedDate,
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Praktikum yang disetujui',
            'status' => 'pending',
        ]);
        Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => $selectedDate,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'purpose' => 'Request yang belum disetujui',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post('/admin/reservations/'.$reservation->id.'/approve')
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'approved',
        ]);

        $this->actingAs($student)
            ->get('/rooms/'.$room->id.'?date='.$selectedDate)
            ->assertOk()
            ->assertSee('Status Tanggal Terpilih')
            ->assertSee('name="date"', false)
            ->assertSee('min="'.today()->toDateString().'"', false)
            ->assertSee($selectedDate)
            ->assertSee('Reservasi: Praktikum yang disetujui')
            ->assertSee('RESERVED')
            ->assertSee('08:00 - 10:00')
            ->assertSee('Kosong (Tersedia untuk Pengajuan)')
            ->assertSee('Ajukan Reservasi')
            ->assertSee('start_time=08%3A00', false)
            ->assertSee('date='.$selectedDate, false);

        $this->actingAs($student)
            ->get('/rooms?date='.$selectedDate)
            ->assertOk()
            ->assertSee('min="'.today()->toDateString().'"', false)
            ->assertSee('Tersedia');
    }

    public function test_room_list_status_uses_the_selected_date_and_daily_availability(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $building = Building::create(['code' => 'BLD-ROOM-FULL', 'name' => 'Gedung Penuh']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-FULL',
            'name' => 'Ruang Penuh',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        $selectedDate = today()->addDays(2)->format('Y-m-d');

        foreach ([['08:00', '10:00'], ['10:00', '12:00'], ['13:00', '15:00'], ['15:00', '17:00']] as [$startTime, $endTime]) {
            Reservation::create([
                'user_id' => $student->id,
                'room_id' => $room->id,
                'date' => $selectedDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'purpose' => 'Kegiatan terjadwal',
                'status' => 'approved',
            ]);
        }

        $this->actingAs($student)
            ->get('/rooms?date='.$selectedDate)
            ->assertOk()
            ->assertSee('RESERVED');

        $this->actingAs($student)
            ->get('/rooms?date='.today()->format('Y-m-d'))
            ->assertOk()
            ->assertSee('Tersedia');
    }

    public function test_selected_room_date_is_prefilled_in_reservation_form(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $building = Building::create(['code' => 'BLD-ROOM-FORM', 'name' => 'Gedung Form']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-FORM',
            'name' => 'Ruang Form',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        $selectedDate = today()->addDays(3)->format('Y-m-d');

        $this->actingAs($student)
            ->get('/reservations/create?'.http_build_query([
                'room_id' => $room->id,
                'date' => $selectedDate,
            ]))
            ->assertOk()
            ->assertSee('name="date"', false)
            ->assertSee('min="'.today()->toDateString().'"', false)
            ->assertSee('value="'.$selectedDate.'"', false);

        $this->actingAs($student)
            ->get('/reservations/create?'.http_build_query([
                'room_id' => $room->id,
                'date' => today()->subDay()->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertSee('value="'.today()->addDay()->format('Y-m-d').'"', false);
    }

    public function test_room_pages_reject_dates_before_today(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $building = Building::create(['code' => 'BLD-ROOM-PAST', 'name' => 'Gedung Lampau']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-PAST',
            'name' => 'Ruang Lampau',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        $pastDate = today()->subDay()->format('Y-m-d');

        $this->actingAs($student)
            ->get('/rooms?date='.$pastDate)
            ->assertSessionHasErrors('date');

        $this->actingAs($student)
            ->get('/rooms/'.$room->id.'?date='.$pastDate)
            ->assertSessionHasErrors('date');
    }
}
