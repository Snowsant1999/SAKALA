<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Floor;
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
                    'program' => $u->studyProgram?->name ?? ($u->department?->name ?? 'Belum ditentukan'),
                    'semester' => $u->semester ?? '—',
                    'ipk' => $u->ipk ?? '—',
                    'status' => $u->status === 'active' ? 'Aktif' : 'Nonaktif',
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
                    'department' => $u->department?->name ?? 'Belum ditentukan',
                    'specialization' => $u->specialization ?? 'Belum tersedia',
                    'courses_count' => $u->taughtClasses()->count(),
                    'status' => $u->status === 'active' ? 'Aktif' : 'Nonaktif',
                ];
            });

        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->get()
            ->map(fn (User $admin): array => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'status' => $admin->status === 'active' ? 'Aktif' : 'Nonaktif',
            ]);

        return view('admin.master.users', compact('students', 'lecturers', 'admins'));
    }

    public function students()
    {
        $students = User::where('role', 'mahasiswa')
            ->with(['studyProgram', 'department', 'courseClasses'])
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nim' => $u->nim_nip ?? '2105123456',
                    'email' => $u->email,
                    'program' => $u->studyProgram?->name ?? ($u->department?->name ?? 'Belum ditentukan'),
                    'class' => $u->courseClasses->pluck('name')->join(', ') ?: 'Belum ditetapkan',
                    'semester' => $u->semester ?? '—',
                    'ipk' => $u->ipk ?? '—',
                    'status' => $u->status === 'active' ? 'Aktif' : 'Nonaktif',
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
                    'department' => $u->department?->name ?? 'Belum ditentukan',
                    'specialization' => $u->specialization ?? 'Belum tersedia',
                    'courses_count' => $u->taughtClasses()->count(),
                    'status' => $u->status === 'active' ? 'Aktif' : 'Nonaktif',
                ];
            });

        return view('admin.master.lecturers', compact('lecturers'));
    }

    public function departments()
    {
        $departments = Department::withCount(['studyPrograms', 'students'])
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'code' => $d->code,
                    'name' => $d->name,
                    'head' => $d->head_name ?? 'Belum ditentukan',
                    'programs_count' => $d->study_programs_count,
                    'students_count' => $d->students_count,
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
                    'department' => $p->department?->name ?? 'Belum ditentukan',
                    'degree' => $p->level ?? 'S1',
                    'accreditation' => $p->accreditation ?? 'Belum ditetapkan',
                ];
            });

        return view('admin.master.study_programs', compact('programs'));
    }

    public function courses()
    {
        $courses = Course::with(['studyProgram', 'classes.lecturer'])
            ->withCount('classes')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'code' => $c->code,
                    'name' => $c->name,
                    'sks' => $c->credits,
                    'semester' => $c->semester,
                    'lecturer' => $c->classes->pluck('lecturer.name')->filter()->unique()->join(', ') ?: 'Belum ditetapkan',
                    'study_program' => $c->studyProgram?->name ?? 'Belum ditautkan',
                    'classes_count' => $c->classes_count,
                ];
            });

        return view('admin.master.courses', compact('courses'));
    }

    public function classes()
    {
        $classes = CourseClass::with(['course.studyProgram', 'lecturer'])
            ->withCount('students')
            ->get()
            ->map(function ($cls) {
                return [
                    'id' => $cls->id,
                    'name' => $cls->name,
                    'program' => $cls->course?->studyProgram?->name ?? 'Belum ditautkan',
                    'academic_year' => $cls->academic_year ?? 'Belum ditentukan',
                    'homeroom' => $cls->lecturer?->name ?? 'Belum ditetapkan',
                    'total_students' => $cls->students_count,
                ];
            });

        return view('admin.master.classes', compact('classes'));
    }

    public function buildings()
    {
        $buildings = Building::withCount(['rooms', 'floors'])
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'code' => $b->code,
                    'name' => $b->name,
                    'floors_count' => $b->floors_count,
                    'rooms_count' => $b->rooms_count,
                    'location' => $b->location ?? 'Belum ditentukan',
                ];
            });

        return view('admin.master.buildings', compact('buildings'));
    }

    public function rooms()
    {
        $now = now();
        $day = $now->locale('id')->isoFormat('dddd');

        $rooms = Room::with([
            'building',
            'floorRecord',
            'schedules' => fn ($query) => $query->where('day', $day)
                ->where('mode', 'ONSITE')
                ->whereTime('start_time', '<=', $now->format('H:i:s'))
                ->whereTime('end_time', '>=', $now->format('H:i:s')),
            'reservations' => fn ($query) => $query->where('date', $now->format('Y-m-d'))
                ->where('status', 'approved')
                ->whereTime('start_time', '<=', $now->format('H:i:s'))
                ->whereTime('end_time', '>=', $now->format('H:i:s')),
        ])
            ->get()
            ->map(function ($r) {
                $status = $r->is_maintenance
                    ? 'MAINTENANCE'
                    : ($r->schedules->isNotEmpty() ? 'DIGUNAKAN' : ($r->reservations->isNotEmpty() ? 'RESERVED' : 'KOSONG'));

                return [
                    'id' => $r->id,
                    'code' => $r->code,
                    'name' => $r->name,
                    'building' => $r->building?->name ?? 'Belum ditentukan',
                    'floor' => $r->floorRecord?->label ?? ('Lantai '.$r->floor),
                    'capacity' => $r->capacity,
                    'type' => $r->type ?? 'Kelas',
                    'status' => $status,
                ];
            });

        return view('admin.master.rooms', compact('rooms'));
    }

    public function floors(Request $request)
    {
        $buildings = Building::orderBy('name')->get(['id', 'name']);
        $floors = Floor::with('building')
            ->withCount('rooms')
            ->when($request->filled('building_id'), fn ($query) => $query->where('building_id', $request->integer('building_id')))
            ->orderBy('building_id')
            ->orderBy('number')
            ->get()
            ->map(fn (Floor $floor): array => [
                'id' => $floor->id,
                'number' => $floor->number,
                'label' => $floor->label,
                'building' => $floor->building?->name ?? 'Gedung tidak tersedia',
                'rooms_count' => $floor->rooms_count,
            ]);

        return view('admin.master.floors', compact('floors', 'buildings'));
    }

    public function schedules()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $programs = StudyProgram::orderBy('name')->pluck('name')->all();

        $allSchedules = Schedule::with(['courseClass.course.studyProgram', 'courseClass.lecturer', 'room'])
            ->get();

        $grouped = $allSchedules->groupBy('day');
        $schedules = [];

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
                    'program' => $s->courseClass->course->studyProgram->name ?? '',
                    'mode' => $s->mode ?? 'ONSITE',
                ];
            })->toArray();
        }

        return view('admin.master.schedules', compact('schedules', 'days', 'programs'));
    }
}
