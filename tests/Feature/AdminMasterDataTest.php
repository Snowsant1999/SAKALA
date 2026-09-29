<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\StudyProgram;
use App\Models\User;
use Database\Seeders\AdditionalCourseClassesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_lecturer_edit_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $lecturer = User::factory()->create(['role' => 'dosen', 'name' => 'Test Lecturer', 'status' => 'active']);

        $this->actingAs($admin)
            ->get('/admin/lecturers/'.$lecturer->id.'/edit')
            ->assertOk()
            ->assertSee('Test Lecturer');
    }

    public function test_additional_course_classes_seeder_is_idempotent(): void
    {
        $department = Department::create(['code' => 'TI', 'name' => 'Jurusan Teknologi Informasi']);
        $ifProgram = StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'IF',
            'name' => 'Teknik Informatika (S1)',
            'level' => 'S1',
        ]);
        $trkProgram = StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'TRK',
            'name' => 'Teknologi Rekayasa Komputer (D4)',
            'level' => 'D4',
        ]);
        $timProgram = StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'TIM',
            'name' => 'Teknik Informatika Multimedia',
            'level' => 'D4',
        ]);
        $webCourse = Course::create(['code' => 'TI-401', 'name' => 'Pemrograman Web', 'credits' => 3, 'semester' => 5]);
        $databaseCourse = Course::create([
            'study_program_id' => $ifProgram->id,
            'code' => 'TI-402',
            'name' => 'Basis Data Lanjut',
            'credits' => 3,
            'semester' => 5,
        ]);
        $softwareCourse = Course::create(['code' => 'TI-403', 'name' => 'Rekayasa Perangkat Lunak', 'credits' => 3, 'semester' => 5]);
        $webLecturer = User::factory()->create(['role' => 'dosen', 'nim_nip' => '198001012005011002']);
        $databaseLecturer = User::factory()->create(['role' => 'dosen', 'nim_nip' => '198503152010122001']);

        $this->seed(AdditionalCourseClassesSeeder::class);
        CourseClass::where('course_id', $softwareCourse->id)
            ->where('name', 'TI 5B')
            ->update(['academic_year' => '2025/2026']);
        $this->seed(AdditionalCourseClassesSeeder::class);

        $this->assertDatabaseHas('course_classes', ['course_id' => $webCourse->id, 'name' => 'TRK 5A', 'lecturer_id' => $webLecturer->id]);
        $this->assertDatabaseHas('course_classes', ['course_id' => $databaseCourse->id, 'name' => 'TRK 5A', 'lecturer_id' => $databaseLecturer->id]);
        $this->assertDatabaseHas('course_classes', ['course_id' => $databaseCourse->id, 'name' => 'TI 5A', 'lecturer_id' => $databaseLecturer->id]);
        $this->assertDatabaseHas('course_classes', ['course_id' => $databaseCourse->id, 'name' => 'TIM 5A', 'lecturer_id' => $databaseLecturer->id]);
        $this->assertDatabaseHas('course_classes', ['course_id' => $softwareCourse->id, 'name' => 'TI 5B', 'lecturer_id' => $webLecturer->id]);
        $this->assertDatabaseCount('course_classes', 5);
        $this->assertSame(3, CourseClass::where('course_id', $databaseCourse->id)->distinct('cohort_id')->count('cohort_id'));
        $this->assertSame('2025/2026', CourseClass::where('course_id', $softwareCourse->id)->value('academic_year'));
        $this->assertSame(0, CourseClass::whereNull('academic_year')->count());
        $this->assertSame($timProgram->id, Cohort::where('name', 'TIM 5A')->value('study_program_id'));
        $this->assertSame($trkProgram->id, Cohort::where('name', 'TRK 5A')->value('study_program_id'));
        $this->assertDatabaseHas('course_classes', [
            'course_id' => $webCourse->id,
            'name' => 'TRK 5A',
            'cohort_id' => Cohort::where('name', 'TRK 5A')->value('id'),
        ]);
        $this->assertDatabaseHas('course_classes', [
            'course_id' => $databaseCourse->id,
            'name' => 'TRK 5A',
            'cohort_id' => Cohort::where('name', 'TRK 5A')->value('id'),
        ]);
    }

    public function test_a_student_cohort_groups_multiple_course_classes(): void
    {
        $studyProgram = StudyProgram::create([
            'department_id' => Department::create(['code' => 'COHORT-DEP', 'name' => 'Jurusan Cohort'])->id,
            'code' => 'COHORT-PROG',
            'name' => 'Program Cohort',
            'level' => 'S1',
        ]);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'status' => 'active',
            'study_program_id' => $studyProgram->id,
        ]);
        $cohort = Cohort::create(['study_program_id' => $studyProgram->id, 'name' => 'IF 5A']);
        $webCourse = Course::create(['code' => 'COHORT-WEB', 'name' => 'Pemrograman Web', 'credits' => 3, 'semester' => 5]);
        $databaseCourse = Course::create(['code' => 'COHORT-DB', 'name' => 'Basis Data', 'credits' => 3, 'semester' => 5]);
        $webClass = CourseClass::create([
            'course_id' => $webCourse->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $cohort->id,
            'name' => $cohort->name,
        ]);
        $databaseClass = CourseClass::create([
            'course_id' => $databaseCourse->id,
            'lecturer_id' => $lecturer->id,
            'cohort_id' => $cohort->id,
            'name' => $cohort->name,
        ]);
        $student->update(['cohort_id' => $cohort->id]);

        $this->assertCount(2, $cohort->courseClasses);
        $this->assertTrue($webClass->cohort->is($cohort));
        $this->assertTrue($databaseClass->cohort->is($cohort));
        $this->assertTrue($cohort->students()->whereKey($student->id)->exists());
        $this->assertTrue($student->cohort->is($cohort));
    }

    public function test_admin_assigns_courses_from_a_cohort_and_the_class_list_groups_by_cohort(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $webCourse = Course::create(['code' => 'MULTI-WEB', 'name' => 'Pemrograman Web', 'credits' => 3, 'semester' => 5]);
        $databaseCourse = Course::create(['code' => 'MULTI-DB', 'name' => 'Basis Data', 'credits' => 3, 'semester' => 5]);
        $firstCohort = Cohort::create(['name' => 'TIM 5A']);
        $secondCohort = Cohort::create(['name' => 'TIM 5B']);

        $this->actingAs($admin)
            ->get('/admin/classes/create?cohort_id='.$firstCohort->id)
            ->assertOk()
            ->assertSee('Tambah Mata Kuliah ke Rombongan')
            ->assertSee('name="cohort_id"', false)
            ->assertSee('<option value="'.$firstCohort->id.'" selected>TIM 5A</option>', false)
            ->assertSee('Pemrograman Web');

        foreach ([
            [$webCourse, $firstCohort],
            [$databaseCourse, $firstCohort],
            [$webCourse, $secondCohort],
        ] as [$course, $cohort]) {
            $this->post('/admin/classes', [
                'course_id' => $course->id,
                'lecturer_id' => $lecturer->id,
                'cohort_id' => $cohort->id,
                'academic_year' => '2026/2027',
            ])
                ->assertRedirect('/admin/classes')
                ->assertSessionHas('success');
        }

        $this->assertDatabaseCount('course_classes', 3);
        $this->assertDatabaseHas('course_classes', [
            'course_id' => $webCourse->id,
            'cohort_id' => $firstCohort->id,
            'name' => $firstCohort->name,
            'academic_year' => '2026/2027',
        ]);
        $this->assertDatabaseHas('course_classes', [
            'course_id' => $webCourse->id,
            'cohort_id' => $secondCohort->id,
            'name' => $secondCohort->name,
            'academic_year' => '2026/2027',
        ]);

        $response = $this->get('/admin/classes')
            ->assertOk()
            ->assertSee('Pemrograman Web')
            ->assertSee('Basis Data')
            ->assertSee('2026/2027');

        $this->assertSame(2, substr_count($response->getContent(), 'data-cohort-row'));
    }

    public function test_admin_cannot_create_a_course_class_without_an_academic_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $course = Course::create(['code' => 'NO-YEAR', 'name' => 'Mata Kuliah Tanpa Tahun', 'credits' => 3, 'semester' => 1]);
        $cohort = Cohort::create(['name' => 'IF 1A']);

        $this->actingAs($admin)
            ->post('/admin/classes', [
                'course_id' => $course->id,
                'lecturer_id' => $lecturer->id,
                'cohort_id' => $cohort->id,
            ])
            ->assertSessionHasErrors('academic_year');

        $this->assertDatabaseCount('course_classes', 0);
    }

    public function test_admin_can_change_a_students_classes_from_the_student_edit_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);
        $existingCohort = Cohort::create(['name' => 'Kelas Lama']);
        $newCohort = Cohort::create(['name' => 'Kelas Baru']);
        $course = Course::create(['code' => 'TEST-COURSE', 'name' => 'Mata Kuliah Uji', 'credits' => 3, 'semester' => 1]);
        $existingClass = CourseClass::create(['course_id' => $course->id, 'lecturer_id' => $lecturer->id, 'cohort_id' => $existingCohort->id, 'name' => 'Kelas Lama']);
        $otherCourse = Course::create(['code' => 'OTHER-COURSE', 'name' => 'Mata Kuliah Lain', 'credits' => 3, 'semester' => 1]);
        $parallelClass = CourseClass::create(['course_id' => $otherCourse->id, 'lecturer_id' => $lecturer->id, 'cohort_id' => $existingCohort->id, 'name' => 'Kelas Lama']);
        $student->update(['cohort_id' => $existingCohort->id]);

        $this->actingAs($admin)
            ->get('/admin/students/'.$student->id.'/edit')
            ->assertSee('<select id="field-cohort_id" name="cohort_id"', false)
            ->assertSee('<option value="'.$existingCohort->id.'" selected>Kelas Lama</option>', false)
            ->assertDontSee('Mata Kuliah Uji');

        $this->assertTrue($existingClass->students()->whereKey($student->id)->exists());
        $this->assertTrue($parallelClass->students()->whereKey($student->id)->exists());

        $this->actingAs($admin)
            ->put('/admin/students/'.$student->id, [
                'name' => $student->name,
                'email' => $student->email,
                'status' => 'active',
                'cohort_id' => $newCohort->id,
            ])
            ->assertRedirect('/admin/students')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'cohort_id' => $newCohort->id,
        ]);
        $this->assertFalse($existingClass->fresh()->students()->whereKey($student->id)->exists());
        $this->assertFalse($parallelClass->fresh()->students()->whereKey($student->id)->exists());
    }

    public function test_admin_cannot_assign_a_student_to_an_unknown_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);

        $this->actingAs($admin)
            ->put('/admin/students/'.$student->id, [
                'name' => $student->name,
                'email' => $student->email,
                'status' => 'active',
                'cohort_id' => 999999,
            ])
            ->assertSessionHasErrors('cohort_id');

        $this->assertDatabaseMissing('course_class_student', [
            'user_id' => $student->id,
        ]);
    }

    public function test_a_students_cohort_roster_applies_to_each_course_class(): void
    {
        $lecturer = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $cohort = Cohort::create(['name' => 'TIM 5A']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active', 'cohort_id' => $cohort->id]);
        $course = Course::create(['code' => 'TEST-COURSE', 'name' => 'Mata Kuliah Uji', 'credits' => 3, 'semester' => 1]);
        $firstClass = CourseClass::create(['course_id' => $course->id, 'lecturer_id' => $lecturer->id, 'cohort_id' => $cohort->id, 'name' => 'TIM 5A']);
        $secondClass = CourseClass::create(['course_id' => $course->id, 'lecturer_id' => $lecturer->id, 'cohort_id' => $cohort->id, 'name' => 'TIM 5A']);

        $this->assertTrue($firstClass->students()->whereKey($student->id)->exists());
        $this->assertTrue($secondClass->students()->whereKey($student->id)->exists());
    }

    public function test_admin_can_create_a_cohort(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $department = Department::create(['code' => 'COHORT-DEP', 'name' => 'Jurusan Cohort']);
        $program = StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'COHORT-PROG',
            'name' => 'Program Cohort',
            'level' => 'S1',
        ]);

        $this->actingAs($admin)
            ->post('/admin/cohorts', ['name' => 'IF 5A', 'study_program_id' => $program->id])
            ->assertRedirect('/admin/cohorts')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cohorts', [
            'name' => 'IF 5A',
            'study_program_id' => $program->id,
        ]);

        $this->get('/admin/cohorts')->assertOk()->assertSee('IF 5A');
    }

    public function test_admin_can_create_department(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->post('/admin/departments', [
                'code' => 'TEST-DEP',
                'name' => 'Jurusan Uji',
                'head_name' => 'Kepala Uji',
            ])
            ->assertRedirect('/admin/departments')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('departments', [
            'code' => 'TEST-DEP',
            'name' => 'Jurusan Uji',
            'head_name' => 'Kepala Uji',
        ]);
    }

    public function test_admin_can_view_department_with_program_and_student_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $department = Department::create(['code' => 'COUNT-DEP', 'name' => 'Jurusan Hitung']);
        StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'COUNT-PROG',
            'name' => 'Program Hitung',
            'level' => 'S1',
        ]);
        User::factory()->create([
            'department_id' => $department->id,
            'role' => 'mahasiswa',
            'status' => 'active',
        ]);
        User::factory()->create([
            'department_id' => $department->id,
            'role' => 'dosen',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get('/admin/departments')
            ->assertOk()
            ->assertSee('Jurusan Hitung')
            ->assertSee('1 Prodi')
            ->assertSee('1 Orang');
    }

    public function test_department_with_programs_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $department = Department::create(['code' => 'TEST-DEP', 'name' => 'Jurusan Uji']);
        StudyProgram::create([
            'department_id' => $department->id,
            'code' => 'TEST-PROG',
            'name' => 'Program Uji',
            'level' => 'S1',
        ]);

        $this->actingAs($admin)
            ->delete('/admin/departments/'.$department->id)
            ->assertSessionHas('error');

        $this->assertModelExists($department);
    }

    public function test_non_admin_cannot_create_department(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'status' => 'active']);

        $this->actingAs($student)
            ->post('/admin/departments', [
                'code' => 'DENIED',
                'name' => 'Tidak Diizinkan',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('departments', ['code' => 'DENIED']);
    }
}
