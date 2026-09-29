<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Cohort;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\ScheduleException;
use App\Models\StudyProgram;
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
        /** @var User $lecturer */
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $department = Department::create(['code' => 'DEP-ROOM-TEST', 'name' => 'Teknik Uji']);
        $studyProgram = StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'PROG-ROOM-TEST',
            'name' => 'Teknik Informatika Uji',
        ]);
        $cohort = Cohort::create([
            'study_program_id' => $studyProgram->id,
            'name' => 'Angkatan 2026 Uji',
        ]);
        $course = Course::create([
            'code' => 'COURSE-ROOM-TEST',
            'name' => 'Praktikum Jaringan Uji',
            'credits' => 3,
            'semester' => 5,
        ]);
        $courseClass = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $cohort->id,
            'name' => '5A Uji',
        ]);
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
            'course_id' => $course->id,
            'course_class_id' => $courseClass->id,
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
        Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => $selectedDate,
            'start_time' => '15:00',
            'end_time' => '17:00',
            'purpose' => 'Kegiatan umum tanpa kelas',
            'status' => 'approved',
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
            ->assertSee('Teknik Informatika Uji 5A Uji')
            ->assertSee('Reservasi: Kegiatan umum tanpa kelas')
            ->assertSee('Tidak terkait prodi Tidak terkait kelas')
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

    public function test_lecturer_can_cancel_one_scheduled_occurrence_for_reservation(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        /** @var User $lecturer */
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        /** @var User $otherLecturer */
        $otherLecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $department = Department::create(['code' => 'DEP-OCCURRENCE', 'name' => 'Jurusan Sesi']);
        $studyProgram = StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'PROG-OCCURRENCE',
            'name' => 'Program Sesi',
        ]);
        $cohort = Cohort::create([
            'study_program_id' => $studyProgram->id,
            'name' => 'Kelas Sesi',
        ]);
        $student->update(['cohort_id' => $cohort->id]);
        $course = Course::create([
            'code' => 'COURSE-OCCURRENCE',
            'name' => 'Praktikum Mingguan Uji',
            'credits' => 3,
            'semester' => 5,
        ]);
        $courseClass = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $cohort->id,
            'name' => 'Kelas Sesi',
        ]);
        $building = Building::create(['code' => 'BLD-OCCURRENCE', 'name' => 'Gedung Sesi']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-OCCURRENCE',
            'name' => 'Ruang Sesi',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        $schedule = Schedule::create([
            'course_class_id' => $courseClass->id,
            'room_id' => $room->id,
            'day' => today()->locale('id')->isoFormat('dddd'),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'mode' => 'ONSITE',
        ]);
        $sessionDate = today()->addWeek()->format('Y-m-d');
        $reservationPayload = [
            'room_id' => $room->id,
            'course_id' => $course->id,
            'course_class_id' => $courseClass->id,
            'date' => $sessionDate,
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Menggunakan ruang setelah sesi dibatalkan',
        ];

        $this->actingAs($otherLecturer)
            ->post('/schedules/'.$schedule->id.'/exceptions', [
                'date' => $sessionDate,
                'reason' => 'Dosen lain tidak boleh mengubah jadwal ini.',
            ])
            ->assertForbidden();

        $this->actingAs($student)
            ->post('/reservations', $reservationPayload)
            ->assertSessionHasErrors('room_id');

        $this->actingAs($lecturer)
            ->post('/schedules/'.$schedule->id.'/exceptions', [
                'date' => $sessionDate,
                'reason' => 'Dosen berhalangan hadir.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->actingAs($student)
            ->post('/reservations', $reservationPayload)
            ->assertRedirect('/reservations');

        $this->assertDatabaseHas('reservations', [
            'room_id' => $room->id,
            'date' => $sessionDate,
            'status' => 'pending',
        ]);

        $this->actingAs($student)
            ->get('/rooms/'.$room->id.'?date='.$sessionDate)
            ->assertOk()
            ->assertSee('Sesi dibatalkan')
            ->assertSee('start_time=13%3A00', false);

        $exception = ScheduleException::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $sessionDate)
            ->firstOrFail();

        $this->actingAs($student)
            ->delete('/schedules/'.$schedule->id.'/exceptions/'.$exception->id)
            ->assertForbidden();

        $reservation = Reservation::query()
            ->where('room_id', $room->id)
            ->whereDate('date', $sessionDate)
            ->firstOrFail();
        $this->actingAs($admin)
            ->post('/admin/reservations/'.$reservation->id.'/approve')
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'approved',
        ]);

        $this->actingAs($student)
            ->get('/rooms/'.$room->id.'?date='.$sessionDate)
            ->assertOk()
            ->assertSee('RESERVED');

        $nextOccurrence = today()->addWeeks(2)->format('Y-m-d');
        $this->actingAs($student)
            ->get('/rooms/'.$room->id.'?date='.$nextOccurrence)
            ->assertOk()
            ->assertSee('Praktikum Mingguan Uji')
            ->assertDontSee('start_time=13%3A00', false);

        $this->actingAs($admin)
            ->from('/rooms/'.$room->id.'?date='.$sessionDate)
            ->delete('/schedules/'.$schedule->id.'/exceptions/'.$exception->id)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('schedule_exceptions', ['id' => $exception->id]);

        $unreleasedReservation = Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'course_id' => $course->id,
            'course_class_id' => $courseClass->id,
            'date' => $nextOccurrence,
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Tidak boleh menyetujui slot kuliah rutin',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->from('/admin/reservations')
            ->post('/admin/reservations/'.$unreleasedReservation->id.'/approve')
            ->assertRedirect('/admin/reservations')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('reservations', [
            'id' => $unreleasedReservation->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post('/schedules/'.$schedule->id.'/exceptions', [
                'date' => $nextOccurrence,
                'reason' => 'Kelas dialihkan untuk tanggal ini.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->post('/admin/reservations/'.$unreleasedReservation->id.'/approve')
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $unreleasedReservation->id,
            'status' => 'approved',
        ]);

        $nextException = ScheduleException::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $nextOccurrence)
            ->firstOrFail();
        $this->actingAs($admin)
            ->from('/rooms/'.$room->id.'?date='.$nextOccurrence)
            ->delete('/schedules/'.$schedule->id.'/exceptions/'.$nextException->id)
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}
