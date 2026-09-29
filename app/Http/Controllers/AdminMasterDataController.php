<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Cohort;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminMasterDataController extends Controller
{
    public function create(string $resource): View
    {
        $this->modelClass($resource);

        return $this->form($resource, null);
    }

    public function edit(string $resource, int $id): View
    {
        $modelClass = $this->modelClass($resource);
        $record = $modelClass::query()->findOrFail($id);
        $this->assertResourceRole($resource, $record);

        return $this->form($resource, $record);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $this->modelClass($resource);
        $data = $request->validate($this->validationRules($request, $resource, null));
        $this->validateScheduleSlot($data, null);
        $this->saveResource($request, $resource, null, $data);

        return redirect('/admin/'.$resource)->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $modelClass = $this->modelClass($resource);
        $record = $modelClass::query()->findOrFail($id);
        $this->assertResourceRole($resource, $record);
        $data = $request->validate($this->validationRules($request, $resource, $id));
        $this->validateScheduleSlot($data, $id);
        $this->saveResource($request, $resource, $record, $data);

        return redirect('/admin/'.$resource)->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $modelClass = $this->modelClass($resource);
        $record = $modelClass::query()->findOrFail($id);
        $this->assertResourceRole($resource, $record);

        if ($this->hasDependencies($resource, $record)) {
            return back()->with('error', 'Data masih digunakan dan tidak dapat dihapus.');
        }

        $record->delete();

        return redirect('/admin/'.$resource)->with('success', 'Data berhasil dihapus.');
    }

    private function form(string $resource, ?Model $record): View
    {
        $resourceTitle = $this->resourceTitle($resource);
        $fields = $this->formFields($resource, $record === null);
        $recordValues = $record?->toArray() ?? [];

        return view('admin.master.form', [
            'resource' => $resource,
            'resourceTitle' => $resourceTitle,
            'fields' => $fields,
            'record' => $record,
            'recordValues' => $recordValues,
            'formAction' => $record
                ? url('/admin/'.$resource.'/'.$record->getKey())
                : url('/admin/'.$resource),
        ]);
    }

    private function modelClass(string $resource): string
    {
        return match ($resource) {
            'users', 'students', 'lecturers' => User::class,
            'cohorts' => Cohort::class,
            'departments' => Department::class,
            'study-programs' => StudyProgram::class,
            'courses' => Course::class,
            'classes' => CourseClass::class,
            'buildings' => Building::class,
            'floors' => Floor::class,
            'rooms' => Room::class,
            'schedules' => Schedule::class,
            default => abort(404),
        };
    }

    private function resourceTitle(string $resource): string
    {
        return match ($resource) {
            'users' => 'Pengguna',
            'cohorts' => 'Rombongan',
            'students' => 'Mahasiswa',
            'lecturers' => 'Dosen',
            'departments' => 'Jurusan',
            'study-programs' => 'Program Studi',
            'courses' => 'Mata Kuliah',
            'classes' => 'Kelas',
            'buildings' => 'Gedung',
            'floors' => 'Lantai',
            'rooms' => 'Ruangan',
            'schedules' => 'Jadwal',
            default => abort(404),
        };
    }

    private function formFields(string $resource, bool $isCreate): array
    {
        $departments = Department::orderBy('name')->get(['id', 'name'])
            ->map(fn (Department $department): array => ['value' => $department->id, 'label' => $department->name])->all();
        $programs = StudyProgram::orderBy('name')->get(['id', 'name'])
            ->map(fn (StudyProgram $program): array => ['value' => $program->id, 'label' => $program->name])->all();
        $lecturers = User::where('role', 'dosen')->orderBy('name')->get(['id', 'name'])
            ->map(fn (User $lecturer): array => ['value' => $lecturer->id, 'label' => $lecturer->name])->all();
        $courses = Course::orderBy('name')->get(['id', 'name'])
            ->map(fn (Course $course): array => ['value' => $course->id, 'label' => $course->name])->all();
        $buildings = Building::orderBy('name')->get(['id', 'name'])
            ->map(fn (Building $building): array => ['value' => $building->id, 'label' => $building->name])->all();
        $floors = Floor::with('building')->orderBy('building_id')->orderBy('number')->get()
            ->map(fn (Floor $floor): array => ['value' => $floor->id, 'label' => $floor->building->name.' - '.$floor->label])->all();
        $classes = CourseClass::with('course')->orderBy('name')->orderBy('id')->get()
            ->map(fn (CourseClass $class): array => ['value' => $class->id, 'label' => $class->name.' - '.$class->course->name])->all();
        $cohorts = Cohort::orderBy('name')->get(['id', 'name'])
            ->map(fn (Cohort $cohort): array => ['value' => $cohort->id, 'label' => $cohort->name])->all();
        $rooms = Room::orderBy('name')->get(['id', 'name'])
            ->map(fn (Room $room): array => ['value' => $room->id, 'label' => $room->name])->all();

        $text = static fn (string $name, string $label, bool $required = true): array => compact('name', 'label', 'required') + ['type' => 'text'];
        $number = static fn (string $name, string $label, bool $required = true, ?string $step = null): array => compact('name', 'label', 'required', 'step') + ['type' => 'number'];
        $select = static fn (string $name, string $label, array $options, bool $required = true, bool $multiple = false): array => compact('name', 'label', 'options', 'required', 'multiple') + ['type' => 'select'];

        return match ($resource) {
            'users', 'students', 'lecturers' => array_values(array_filter([
                $resource === 'users' ? $select('role', 'Peran', [
                    ['value' => 'mahasiswa', 'label' => 'Mahasiswa'],
                    ['value' => 'dosen', 'label' => 'Dosen'],
                    ['value' => 'admin', 'label' => 'Admin'],
                ]) : null,
                $text('name', 'Nama'),
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                $text('nim_nip', $resource === 'lecturers' ? 'NIDN' : 'NIM/NIP', false),
                $resource !== 'lecturers' ? $select('department_id', 'Jurusan', $departments, false) : $select('department_id', 'Jurusan', $departments, false),
                $resource !== 'lecturers' ? $select('study_program_id', 'Program Studi', $programs, false) : null,
                $resource === 'students' ? $select('cohort_id', 'Rombongan / Kelas', $cohorts, false) : null,
                $resource === 'lecturers' || $resource === 'users' ? $text('specialization', 'Bidang Keahlian', false) : null,
                $resource === 'students' || $resource === 'users' ? $number('semester', 'Semester', false) : null,
                $resource === 'students' || $resource === 'users' ? $number('ipk', 'IPK', false, '0.01') : null,
                $resource === 'users' || $resource === 'students' || $resource === 'lecturers'
                    ? $select('status', 'Status', [
                        ['value' => 'active', 'label' => 'Aktif'],
                        ['value' => 'inactive', 'label' => 'Nonaktif'],
                    ], true)
                    : null,
                ['name' => 'password', 'label' => $isCreate ? 'Kata Sandi' : 'Kata Sandi Baru (opsional)', 'type' => 'password', 'required' => $isCreate],
                ['name' => 'password_confirmation', 'label' => 'Konfirmasi Kata Sandi', 'type' => 'password', 'required' => $isCreate],
            ])),
            'departments' => [
                $text('code', 'Kode Jurusan'),
                $text('name', 'Nama Jurusan'),
                $text('head_name', 'Ketua Jurusan', false),
            ],
            'study-programs' => [
                $select('department_id', 'Jurusan', $departments),
                $text('code', 'Kode Program Studi'),
                $text('name', 'Nama Program Studi'),
                $text('level', 'Jenjang'),
                $text('accreditation', 'Akreditasi', false),
            ],
            'courses' => [
                $select('study_program_id', 'Program Studi', $programs, false),
                $text('code', 'Kode Mata Kuliah'),
                $text('name', 'Nama Mata Kuliah'),
                $number('credits', 'SKS'),
                $number('semester', 'Semester'),
            ],
            'cohorts' => [
                $select('study_program_id', 'Program Studi', $programs, false),
                $text('name', 'Nama Rombongan'),
            ],
            'classes' => [
                $select('course_id', 'Mata Kuliah', $courses),
                $select('lecturer_id', 'Dosen Wali', $lecturers),
                $select('cohort_id', 'Rombongan', $cohorts),
                $text('name', 'Nama Kelas'),
                $text('academic_year', 'Tahun Akademik', false),
            ],
            'buildings' => [
                $select('department_id', 'Jurusan', $departments, false),
                $text('code', 'Kode Gedung'),
                $text('name', 'Nama Gedung'),
                $text('location', 'Lokasi', false),
            ],
            'floors' => [
                $select('building_id', 'Gedung', $buildings),
                $number('number', 'Nomor Lantai'),
                $text('label', 'Label Lantai'),
            ],
            'rooms' => [
                $select('floor_id', 'Gedung dan Lantai', $floors),
                $text('code', 'Kode Ruangan'),
                $text('name', 'Nama Ruangan'),
                $number('capacity', 'Kapasitas'),
                $text('type', 'Tipe Ruangan'),
                ['name' => 'is_maintenance', 'label' => 'Sedang maintenance', 'type' => 'checkbox', 'required' => false],
            ],
            'schedules' => [
                $select('course_class_id', 'Kelas', $classes),
                $select('room_id', 'Ruangan', $rooms),
                $select('day', 'Hari', array_map(fn (string $day): array => ['value' => $day, 'label' => $day], ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])),
                ['name' => 'start_time', 'label' => 'Jam Mulai', 'type' => 'time', 'required' => true],
                ['name' => 'end_time', 'label' => 'Jam Selesai', 'type' => 'time', 'required' => true],
                $select('mode', 'Mode', [
                    ['value' => 'ONSITE', 'label' => 'Tatap muka'],
                    ['value' => 'ONLINE', 'label' => 'Daring'],
                    ['value' => 'CANCELLED', 'label' => 'Dibatalkan'],
                ]),
            ],
            default => abort(404),
        };
    }

    private function validationRules(Request $request, string $resource, ?int $id): array
    {
        $unique = static fn (string $table, string $column = 'code') => $id === null
            ? Rule::unique($table, $column)
            : Rule::unique($table, $column)->ignore($id);

        return match ($resource) {
            'users', 'students', 'lecturers' => [
                'role' => $resource === 'users' ? ['required', Rule::in(['admin', 'dosen', 'mahasiswa'])] : ['prohibited'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', $unique('users', 'email')],
                'nim_nip' => ['nullable', 'string', 'max:255', $unique('users', 'nim_nip')],
                'department_id' => ['nullable', 'exists:departments,id'],
                'study_program_id' => ['nullable', 'exists:study_programs,id'],
                'cohort_id' => $resource === 'students' ? ['nullable', 'exists:cohorts,id'] : ['prohibited'],
                'specialization' => ['nullable', 'string', 'max:255'],
                'semester' => ['nullable', 'integer', 'min:1', 'max:20'],
                'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
                'class_id' => ['prohibited'],
                'status' => ['required', Rule::in(['active', 'inactive'])],
                'password' => [$id === null ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
                'password_confirmation' => [$id === null ? 'required_with:password' : 'nullable', 'string'],
            ],
            'departments' => [
                'code' => ['required', 'string', 'max:255', $unique('departments')],
                'name' => ['required', 'string', 'max:255'],
                'head_name' => ['nullable', 'string', 'max:255'],
            ],
            'study-programs' => [
                'department_id' => ['required', 'exists:departments,id'],
                'code' => ['required', 'string', 'max:255', $unique('study_programs')],
                'name' => ['required', 'string', 'max:255'],
                'level' => ['required', 'string', 'max:255'],
                'accreditation' => ['nullable', 'string', 'max:255'],
            ],
            'courses' => [
                'study_program_id' => ['nullable', 'exists:study_programs,id'],
                'code' => ['required', 'string', 'max:255', $unique('courses')],
                'name' => ['required', 'string', 'max:255'],
                'credits' => ['required', 'integer', 'min:1', 'max:40'],
                'semester' => ['required', 'integer', 'min:1', 'max:20'],
            ],
            'cohorts' => [
                'study_program_id' => ['nullable', 'exists:study_programs,id'],
                'name' => ['required', 'string', 'max:255', $unique('cohorts', 'name')],
            ],
            'classes' => [
                'course_id' => ['required', 'exists:courses,id'],
                'lecturer_id' => ['required', Rule::exists('users', 'id')->where('role', 'dosen')],
                'cohort_id' => ['required', 'exists:cohorts,id'],
                'name' => ['required', 'string', 'max:255'],
                'academic_year' => ['nullable', 'string', 'max:32'],
            ],
            'buildings' => [
                'department_id' => ['nullable', 'exists:departments,id'],
                'code' => ['required', 'string', 'max:255', $unique('buildings')],
                'name' => ['required', 'string', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],
            ],
            'floors' => [
                'building_id' => ['required', 'exists:buildings,id'],
                'number' => [
                    'required', 'integer', 'min:1',
                    Rule::unique('floors', 'number')->where('building_id', $request->input('building_id'))->ignore($id),
                ],
                'label' => ['required', 'string', 'max:255'],
            ],
            'rooms' => [
                'floor_id' => ['required', 'exists:floors,id'],
                'code' => ['required', 'string', 'max:255', $unique('rooms')],
                'name' => ['required', 'string', 'max:255'],
                'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
                'type' => ['required', 'string', 'max:255'],
                'is_maintenance' => ['sometimes', 'boolean'],
            ],
            'schedules' => [
                'course_class_id' => ['required', 'exists:course_classes,id'],
                'room_id' => ['required', 'exists:rooms,id'],
                'day' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
                'start_time' => ['required', 'date_format:H:i'],
                'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
                'mode' => ['required', Rule::in(['ONSITE', 'ONLINE', 'CANCELLED'])],
            ],
            default => abort(404),
        };
    }

    private function saveResource(Request $request, string $resource, ?Model $record, array $data): void
    {
        unset($data['password_confirmation']);

        if (in_array($resource, ['students', 'lecturers'], true)) {
            $data['role'] = $resource === 'students' ? 'mahasiswa' : 'dosen';
        }

        if (in_array($resource, ['users', 'students', 'lecturers'], true) && empty($data['password'])) {
            unset($data['password']);
        }

        if ($resource === 'rooms') {
            $floor = Floor::findOrFail($data['floor_id']);
            $data['building_id'] = $floor->building_id;
            $data['floor'] = $floor->number;
            $data['is_maintenance'] = $request->boolean('is_maintenance');
        }

        $modelClass = $this->modelClass($resource);
        $record ??= new $modelClass;
        $record->fill($data);
        $record->save();
    }

    private function assertResourceRole(string $resource, Model $record): void
    {
        if ($resource === 'students' && $record->role !== 'mahasiswa') {
            abort(404);
        }

        if ($resource === 'lecturers' && $record->role !== 'dosen') {
            abort(404);
        }
    }

    private function validateScheduleSlot(array $data, ?int $id): void
    {
        if (! isset($data['course_class_id']) || $data['mode'] !== 'ONSITE') {
            return;
        }

        $conflict = Schedule::query()
            ->where('room_id', $data['room_id'])
            ->where('day', $data['day'])
            ->where('mode', 'ONSITE')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->when($id !== null, fn ($query) => $query->where('id', '!=', $id))
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_time' => 'Slot ruangan sudah memiliki jadwal tatap muka yang bertumpang tindih.',
            ]);
        }
    }

    private function hasDependencies(string $resource, Model $record): bool
    {
        return match ($resource) {
            'users' => $record->reservations()->exists()
                || $record->reports()->exists()
                || $record->aspirations()->exists()
                || $record->taughtClasses()->exists()
                || $record->cohort_id !== null
                || $record->submissions()->exists(),
            'students' => $record->reservations()->exists()
                || $record->reports()->exists()
                || $record->aspirations()->exists()
                || $record->cohort_id !== null
                || $record->submissions()->exists(),
            'lecturers' => $record->taughtClasses()->exists(),
            'departments' => $record->studyPrograms()->exists()
                || $record->buildings()->exists()
                || User::where('department_id', $record->id)->exists(),
            'study-programs' => $record->students()->exists() || $record->courses()->exists(),
            'courses' => $record->classes()->exists() || $record->reservations()->exists(),
            'classes' => $record->schedules()->exists()
                || $record->materials()->exists()
                || $record->assignments()->exists()
                || $record->reservations()->exists()
                || $record->students()->exists(),
            'cohorts' => $record->students()->exists() || $record->courseClasses()->exists(),
            'buildings' => $record->rooms()->exists() || $record->floors()->exists(),
            'floors' => $record->rooms()->exists(),
            'rooms' => $record->schedules()->exists() || $record->reservations()->exists(),
            default => false,
        };
    }
}
