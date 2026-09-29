<?php

namespace Tests\Feature;

use App\Models\Aspiration;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Building;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_aspiration_and_unknown_status_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $reporter = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $aspiration = Aspiration::create([
            'reporter_id' => $reporter->id,
            'category' => 'Kerusakan Fasilitas',
            'location' => 'Gedung Uji',
            'description' => 'Proyektor tidak menyala.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post('/admin/aspirations/'.$aspiration->id.'/update', [
                'status' => 'DIPROSES',
                'admin_notes' => 'Teknisi akan memeriksa proyektor.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('aspirations', [
            'id' => $aspiration->id,
            'status' => 'processing',
            'admin_notes' => 'Teknisi akan memeriksa proyektor.',
        ]);

        $this->actingAs($admin)
            ->post('/admin/aspirations/'.$aspiration->id.'/update', [
                'status' => 'INVALID',
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('aspirations', [
            'id' => $aspiration->id,
            'status' => 'processing',
        ]);
    }

    public function test_lecturer_dashboard_shows_own_courses_and_recent_submissions(): void
    {
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $course = Course::create(['code' => 'DOS-101', 'name' => 'Pengelolaan Kelas', 'credits' => 3, 'semester' => 5]);
        $courseClass = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'name' => 'Dosen 5A',
        ]);
        $assignment = Assignment::create([
            'course_class_id' => $courseClass->id,
            'title' => 'Tugas Observasi Kelas',
            'description' => 'Lakukan observasi kelas digital.',
            'deadline' => now()->addDays(3),
        ]);
        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'file_path' => 'submissions/observasi-dosen.zip',
            'score' => 92,
            'feedback' => 'Bagus dan relevan.',
        ]);

        $this->actingAs($lecturer)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Dosen')
            ->assertSee('Pengelolaan Kelas')
            ->assertSee('Pengumpulan Tugas Terbaru')
            ->assertSee($student->name);
    }

    public function test_student_can_submit_report_and_admin_can_update_its_status(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($student)
            ->post('/reports', [
                'category' => 'Intimidasi',
                'incident_date' => today()->format('Y-m-d'),
                'location' => 'Gedung A',
                'involved_parties' => 'Mahasiswa lain',
                'description' => 'Ada ancaman pada saat jam malam.',
            ])
            ->assertRedirect();

        $report = \App\Models\Report::query()->where('reporter_id', $student->id)->firstOrFail();

        $this->actingAs($admin)
            ->post('/admin/reports/'.$report->id.'/update', [
                'status' => 'IN_PROGRESS',
                'priority' => 'HIGH',
                'admin_note' => 'Tim keamanan sedang menelusuri laporan.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        $this->actingAs($student)
            ->get('/reports/'.$report->id)
            ->assertOk()
            ->assertSee('Sedang Ditangani Satgas');
    }

    public function test_student_can_submit_aspiration_and_admin_can_close_it(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($student)
            ->post('/aspirations', [
                'category' => 'Kebersihan',
                'location' => 'Laboratorium Teknik',
                'description' => 'Toilet di laboratorium sering kotor dan tidak ada sabun.',
            ])
            ->assertRedirect();

        $aspiration = Aspiration::query()->where('reporter_id', $student->id)->firstOrFail();

        $this->actingAs($admin)
            ->post('/admin/aspirations/'.$aspiration->id.'/update', [
                'status' => 'SELESAI',
                'admin_notes' => 'Petugas kebersihan telah membersihkan area.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('aspirations', [
            'id' => $aspiration->id,
            'status' => 'resolved',
            'admin_notes' => 'Petugas kebersihan telah membersihkan area.',
        ]);

        $this->actingAs($student)
            ->get('/aspirations/'.$aspiration->id)
            ->assertOk()
            ->assertSee('Selesai Ditangani');
    }

    public function test_conflict_actions_are_only_offered_for_pending_reservations(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $building = Building::create(['code' => 'BLD-CONFLICT', 'name' => 'Gedung Konflik']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-CONFLICT',
            'name' => 'Ruang Konflik',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        $approved = Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => today()->addDay(),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Jadwal yang sudah dikunci',
            'status' => 'approved',
        ]);
        $pending = Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => today()->addDay(),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'purpose' => 'Request yang bentrok',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get('/admin/reservations?tab=conflicts')
            ->assertOk()
            ->assertSee('Sudah Disetujui')
            ->assertSee('Menunggu Review')
            ->assertDontSee('/admin/reservations/'.$approved->id.'/approve')
            ->assertSee('/admin/reservations/'.$pending->id.'/approve');
    }

    public function test_admin_can_filter_completed_and_cancelled_reservations(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $building = Building::create(['code' => 'BLD-HISTORY', 'name' => 'Gedung Riwayat']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-HISTORY',
            'name' => 'Ruang Riwayat',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => today()->subDay(),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Sesi yang selesai',
            'status' => 'approved',
        ]);
        Reservation::create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'date' => today()->addDay(),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Sesi yang dibatalkan',
            'status' => 'cancelled',
        ]);

        $this->actingAs($admin)
            ->get('/admin/reservations?tab=completed')
            ->assertOk()
            ->assertSee('COMPLETED')
            ->assertSee('Sesi yang selesai')
            ->assertDontSee('Sesi yang dibatalkan');

        $this->get('/admin/reservations?tab=cancelled')
            ->assertOk()
            ->assertSee('CANCELLED')
            ->assertSee('Sesi yang dibatalkan')
            ->assertDontSee('Sesi yang selesai');
    }
}
