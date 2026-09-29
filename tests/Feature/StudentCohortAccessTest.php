<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Building;
use App\Models\Cohort;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCohortAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_only_sees_and_opens_course_classes_in_their_cohort(): void
    {
        $studentCohort = Cohort::create(['name' => 'IF 5A']);
        $otherCohort = Cohort::create(['name' => 'TRK 5A']);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'status' => 'active',
            'cohort_id' => $studentCohort->id,
        ]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $course = Course::create(['code' => 'IF-501', 'name' => 'Web Milik Cohort', 'credits' => 3, 'semester' => 5]);
        $otherCourse = Course::create(['code' => 'TRK-501', 'name' => 'Basis Data Cohort Lain', 'credits' => 3, 'semester' => 5]);
        CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $studentCohort->id,
            'name' => $studentCohort->name,
        ]);
        $otherClass = CourseClass::create([
            'course_id' => $otherCourse->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $otherCohort->id,
            'name' => $otherCohort->name,
        ]);

        $this->actingAs($student)
            ->get('/courses')
            ->assertOk()
            ->assertSee('Web Milik Cohort')
            ->assertDontSee('Basis Data Cohort Lain');

        $this->get('/courses/'.$otherClass->id)->assertNotFound();
    }

    public function test_student_dashboard_and_schedule_only_show_their_cohort(): void
    {
        $this->travelTo(Carbon::parse('2026-09-28 07:00:00'));

        $studentCohort = Cohort::create(['name' => 'IF 5A']);
        $otherCohort = Cohort::create(['name' => 'TRK 5A']);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'status' => 'active',
            'cohort_id' => $studentCohort->id,
        ]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $building = Building::create(['code' => 'COHORT-BLD', 'name' => 'Gedung Cohort']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'COHORT-RM',
            'name' => 'Ruang Cohort',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);
        $course = Course::create(['code' => 'IF-501', 'name' => 'Jadwal Milik Cohort', 'credits' => 3, 'semester' => 5]);
        $otherCourse = Course::create(['code' => 'TRK-501', 'name' => 'Jadwal Cohort Lain', 'credits' => 3, 'semester' => 5]);
        $studentClass = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $studentCohort->id,
            'name' => $studentCohort->name,
        ]);
        $otherClass = CourseClass::create([
            'course_id' => $otherCourse->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $otherCohort->id,
            'name' => $otherCohort->name,
        ]);
        Schedule::create([
            'course_class_id' => $studentClass->id,
            'room_id' => $room->id,
            'day' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'mode' => 'ONSITE',
        ]);
        Schedule::create([
            'course_class_id' => $otherClass->id,
            'room_id' => $room->id,
            'day' => 'Senin',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'mode' => 'ONSITE',
        ]);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Jadwal Milik Cohort')
            ->assertDontSee('Jadwal Cohort Lain');

        $this->get('/schedule?day=Senin')
            ->assertOk()
            ->assertSee('Jadwal Milik Cohort')
            ->assertDontSee('Jadwal Cohort Lain');
    }

    public function test_student_submission_is_saved_and_shown_as_submitted(): void
    {
        $cohort = Cohort::create(['name' => 'IF 5A']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active', 'cohort_id' => $cohort->id]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $course = Course::create(['code' => 'IF-SUB', 'name' => 'Tugas Cohort', 'credits' => 3, 'semester' => 5]);
        $class = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $cohort->id,
            'name' => $cohort->name,
        ]);
        $assignment = Assignment::create([
            'course_class_id' => $class->id,
            'title' => 'Tugas Basis',
            'description' => 'Kumpulkan jawaban.',
            'deadline' => now()->addDay(),
            'file_path' => 'assignments/basis.pdf',
        ]);

        $this->actingAs($student)
            ->post('/courses/'.$class->id.'/assignments/'.$assignment->id.'/submit', [
                'file_name' => 'jawaban-basis.zip',
                'notes' => 'Sudah selesai.',
            ])
            ->assertRedirect('/courses/'.$class->id.'/assignments')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'file_path' => 'submissions/jawaban-basis.zip',
        ]);

        $this->get('/courses/'.$class->id.'/assignments')
            ->assertSee('Terkumpul')
            ->assertSee('jawaban-basis.zip');

        $this->get('/dashboard')
            ->assertDontSee('Tugas Basis');
    }

    public function test_student_cannot_submit_an_assignment_from_another_cohort(): void
    {
        $studentCohort = Cohort::create(['name' => 'IF 5A']);
        $otherCohort = Cohort::create(['name' => 'TRK 5A']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active', 'cohort_id' => $studentCohort->id]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $course = Course::create(['code' => 'TRK-SUB', 'name' => 'Tugas Cohort Lain', 'credits' => 3, 'semester' => 5]);
        $class = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $otherCohort->id,
            'name' => $otherCohort->name,
        ]);
        $assignment = Assignment::create([
            'course_class_id' => $class->id,
            'title' => 'Tugas Rahasia',
            'description' => 'Tidak boleh diakses.',
            'deadline' => now()->addDay(),
            'file_path' => 'assignments/private.pdf',
        ]);

        $this->actingAs($student)
            ->post('/courses/'.$class->id.'/assignments/'.$assignment->id.'/submit', [
                'file_name' => 'jawaban.zip',
            ])
            ->assertNotFound();

        $this->assertDatabaseMissing('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_student_cannot_add_material_to_a_course_class(): void
    {
        $cohort = Cohort::create(['name' => 'IF 5A']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active', 'cohort_id' => $cohort->id]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $course = Course::create(['code' => 'IF-MAT', 'name' => 'Materi Cohort', 'credits' => 3, 'semester' => 5]);
        $class = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $cohort->id,
            'name' => $cohort->name,
        ]);

        $this->actingAs($student)
            ->post('/courses/'.$class->id.'/materials', [
                'title' => 'Materi tak berizin',
                'description' => 'Tidak boleh ditambahkan mahasiswa.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('materials', [
            'course_class_id' => $class->id,
            'title' => 'Materi tak berizin',
        ]);
    }

    public function test_unassigned_students_are_not_counted_in_unassigned_class_rosters(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $course = Course::create(['code' => 'UNASSIGNED', 'name' => 'Kelas Belum Dipetakan', 'credits' => 3, 'semester' => 5]);
        $class = CourseClass::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'name' => 'Belum Dipetakan',
        ]);

        $this->assertFalse($class->students()->whereKey($student->id)->exists());
    }

    public function test_reservation_form_only_lists_cohort_classes_and_rejects_other_cohort(): void
    {
        $studentCohort = Cohort::create(['name' => 'IF 5A']);
        $otherCohort = Cohort::create(['name' => 'TRK 5A']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active', 'cohort_id' => $studentCohort->id]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $ownCourse = Course::create(['code' => 'IF-RES', 'name' => 'Reservasi IF', 'credits' => 3, 'semester' => 5]);
        $otherCourse = Course::create(['code' => 'TRK-RES', 'name' => 'Reservasi TRK', 'credits' => 3, 'semester' => 5]);
        CourseClass::create([
            'course_id' => $ownCourse->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $studentCohort->id,
            'name' => $studentCohort->name,
        ]);
        $otherClass = CourseClass::create([
            'course_id' => $otherCourse->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $otherCohort->id,
            'name' => $otherCohort->name,
        ]);
        $building = Building::create(['code' => 'RES-BLD', 'name' => 'Gedung Reservasi']);
        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'RES-RM',
            'name' => 'Ruang Reservasi',
            'capacity' => 30,
            'type' => 'Kelas',
        ]);

        $this->actingAs($student)
            ->get('/reservations/create')
            ->assertOk()
            ->assertSee('Reservasi IF')
            ->assertDontSee('Reservasi TRK');

        $this->post('/reservations', [
            'room_id' => $room->id,
            'course_id' => $otherCourse->id,
            'course_class_id' => $otherClass->id,
            'date' => today()->addDay()->format('Y-m-d'),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Reservasi lintas cohort',
        ])->assertSessionHasErrors('course_class_id');

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $student->id,
            'room_id' => $room->id,
        ]);
    }
}
