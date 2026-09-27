<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMasterController extends Controller
{
    public function users()
    {
        $students = User::where('role', 'mahasiswa')
            ->with(['studyProgram', 'department'])
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nim' => $u->nim_nip ?? '2105123456',
                    'email' => $u->email,
                    'program' => $u->studyProgram->name ?? ($u->department->name ?? 'Teknik Informatika'),
                    'semester' => 5,
                    'ipk' => '3.85',
                    'status' => 'Aktif',
                ];
            });

        $lecturers = User::where('role', 'dosen')
            ->with('department')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nidn' => $u->nim_nip ?? '198001012005011002',
                    'email' => $u->email,
                    'department' => $u->department->name ?? 'Jurusan Teknologi Informasi',
                    'courses_count' => 3,
                    'status' => 'Aktif',
                ];
            });

        return view('admin.master.users', compact('students', 'lecturers'));
    }

    public function students()
    {
        $students = User::where('role', 'mahasiswa')
            ->with(['studyProgram', 'department'])
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nim' => $u->nim_nip ?? '2105123456',
                    'email' => $u->email,
                    'program' => $u->studyProgram->name ?? ($u->department->name ?? 'Teknik Informatika'),
                    'semester' => 5,
                    'ipk' => '3.85',
                    'status' => 'Aktif',
                ];
            });

        return view('admin.master.students', compact('students'));
    }

    public function lecturers()
    {
        $lecturers = User::where('role', 'dosen')
            ->with('department')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nidn' => $u->nim_nip ?? '198001012005011002',
                    'email' => $u->email,
                    'department' => $u->department->name ?? 'Jurusan Teknologi Informasi',
                    'courses_count' => 3,
                    'status' => 'Aktif',
                ];
            });

        return view('admin.master.lecturers', compact('lecturers'));
    }

    public function departments()
    {
        $departments = Department::withCount('studyPrograms')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'code' => $d->code,
                    'name' => $d->name,
                    'head' => 'Dr. Ir. Wahyudi, M.T',
                    'programs_count' => $d->study_programs_count,
                    'students_count' => 120,
                ];
            });

        return view('admin.master.departments', compact('departments'));
    }

    public function studyPrograms()
    {
        $programs = StudyProgram::with('department')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'code' => $p->code,
                    'name' => $p->name,
                    'department' => $p->department->name ?? 'Teknologi Informasi',
                    'degree' => $p->level ?? 'S1',
                    'accreditation' => 'Unggul',
                ];
            });

        return view('admin.master.study_programs', compact('programs'));
    }

    public function courses()
    {
        $courses = Course::with('studyProgram')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'code' => $c->code,
                    'name' => $c->name,
                    'sks' => $c->sks,
                    'semester' => $c->semester,
                    'lecturer' => 'Dr. Budi Santoso',
                    'study_program' => $c->studyProgram->name ?? 'Teknik Informatika',
                ];
            });

        return view('admin.master.courses', compact('courses'));
    }

    public function classes()
    {
        $classes = CourseClass::with(['course.studyProgram', 'lecturer'])
            ->get()
            ->map(function ($cls) {
                return [
                    'id' => $cls->id,
                    'name' => $cls->name,
                    'program' => $cls->course->studyProgram->name ?? 'Teknik Informatika',
                    'academic_year' => $cls->academic_year ?? '2026/2027 Ganjil',
                    'homeroom' => $cls->lecturer->name ?? 'Dr. Budi Santoso, M.Kom',
                    'total_students' => 32,
                ];
            });

        if ($classes->isEmpty()) {
            $classes = [
                ['id' => 1, 'name' => 'IF 5A', 'program' => 'Teknik Informatika', 'academic_year' => '2026/2027 Ganjil', 'homeroom' => 'Dr. Budi Santoso', 'total_students' => 32],
                ['id' => 2, 'name' => 'TRK 5A', 'program' => 'Teknologi Rekayasa Komputer', 'academic_year' => '2026/2027 Ganjil', 'homeroom' => 'Ir. Hendra Wijaya, M.T', 'total_students' => 28],
            ];
        }

        return view('admin.master.classes', compact('classes'));
    }

    public function buildings()
    {
        $buildings = Building::withCount('rooms')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'code' => $b->code,
                    'name' => $b->name,
                    'floors_count' => 4,
                    'rooms_count' => $b->rooms_count,
                    'location' => 'Kampus Politeknik Negeri Samarinda',
                ];
            });

        return view('admin.master.buildings', compact('buildings'));
    }

    public function rooms()
    {
        $now = now();
        $day = $now->locale('id')->isoFormat('dddd');

        $rooms = Room::with('building')
            ->get()
            ->map(function ($r) use ($now, $day) {
                $isScheduled = Schedule::where('room_id', $r->id)
                    ->where('day', $day)
                    ->whereTime('start_time', '<=', $now->format('H:i:s'))
                    ->whereTime('end_time', '>=', $now->format('H:i:s'))
                    ->exists();

                $isReserved = Reservation::where('room_id', $r->id)
                    ->where('date', $now->format('Y-m-d'))
                    ->where('status', 'approved')
                    ->whereTime('start_time', '<=', $now->format('H:i:s'))
                    ->whereTime('end_time', '>=', $now->format('H:i:s'))
                    ->exists();

                $status = $isScheduled ? 'DIGUNAKAN' : ($isReserved ? 'RESERVED' : 'KOSONG');

                return [
                    'id' => $r->id,
                    'code' => $r->code,
                    'name' => $r->name,
                    'building' => $r->building->name ?? 'Gedung TI',
                    'floor' => 'Lantai ' . ($r->floor ?? 1),
                    'capacity' => $r->capacity,
                    'type' => $r->type ?? 'Kelas',
                    'status' => $status,
                ];
            });

        return view('admin.master.rooms', compact('rooms'));
    }

    public function schedules()
    {
        $allSchedules = Schedule::with(['courseClass.course', 'courseClass.lecturer', 'room'])
            ->get();

        $grouped = $allSchedules->groupBy('day');
        $schedules = [];

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($days as $day) {
            $schedules[$day] = ($grouped->get($day, collect()))->map(function ($s) {
                return [
                    'id' => $s->id,
                    'time' => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5),
                    'course' => $s->courseClass->course->name ?? 'Mata Kuliah',
                    'code' => $s->courseClass->course->code ?? '',
                    'lecturer' => $s->courseClass->lecturer->name ?? 'Dosen Pengampu',
                    'room' => $s->room->name ?? ($s->room->code ?? 'Lab'),
                    'class' => $s->courseClass->name ?? '5A',
                    'mode' => $s->mode ?? 'ONSITE',
                ];
            })->toArray();
        }

        return view('admin.master.schedules', compact('schedules'));
    }
}
