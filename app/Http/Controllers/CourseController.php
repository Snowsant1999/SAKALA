<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;

class CourseController extends Controller
{
    /**
     * Helper to format a CourseClass model to array for views.
     */
    private function formatCourseClass(CourseClass $class): array
    {
        $firstSchedule = $class->schedules->first();
        $startTime = $firstSchedule ? substr($firstSchedule->start_time, 0, 5) : '08:00';
        $endTime = $firstSchedule ? substr($firstSchedule->end_time, 0, 5) : '10:00';

        return [
            'id' => $class->id,
            'code' => $class->course?->code ?? 'MK',
            'name' => $class->course?->name ?? 'Mata Kuliah',
            'sks' => $class->course?->credits ?? 3,
            'semester' => $class->course?->semester ?? 5,
            'lecturer' => $class->lecturer?->name ?? 'Dosen Pengampu',
            'lecturer_email' => $class->lecturer?->email ?? '',
            'class' => $class->name,
            'room' => $firstSchedule?->room?->name ?? 'Daring (Zoom)',
            'day' => $firstSchedule?->day ?? 'Senin',
            'time' => $startTime . ' - ' . $endTime,
            'mode' => strtoupper($firstSchedule?->mode ?? 'ONSITE'),
            'department' => 'Teknologi Informasi',
            'study_program' => 'Teknik Informatika (S1)',
            'description' => 'Mata kuliah terstruktur yang membekali mahasiswa dengan kemampuan teoretis dan aplikatif sesuai kurikulum berbasis kompetensi.',
            'students_count' => 32,
        ];
    }

    /**
     * List all courses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = CourseClass::with(['course', 'lecturer', 'schedules.room']);

        // If lecturer, only show classes they teach
        if ($user && $user->role === 'dosen') {
            $query->where('lecturer_id', $user->id);
        }

        // Search filter
        $search = $request->query('q');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('lecturer', function ($lq) use ($search) {
                      $lq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Semester filter
        $semester = $request->query('semester');
        if ($semester && $semester !== 'all') {
            $query->whereHas('course', function ($cq) use ($semester) {
                $cq->where('semester', $semester);
            });
        }

        $classes = $query->get();
        $courses = $classes->map(fn($c) => $this->formatCourseClass($c))->toArray();

        return view('academic.courses.index', compact('courses', 'search', 'semester'));
    }

    /**
     * Show course detail (Overview).
     */
    public function show($id)
    {
        $class = CourseClass::with(['course', 'lecturer', 'schedules.room', 'materials', 'assignments.submissions'])->findOrFail($id);
        $course = $this->formatCourseClass($class);

        $materials = $class->materials->map(function ($m) {
            return [
                'id' => $m->id,
                'title' => $m->title,
                'description' => $m->description ?? 'Modul materi perkuliahan',
                'file_name' => basename($m->file_path ?? 'Materi_Kuliah.pdf'),
                'file_size' => '2.4 MB',
                'uploaded_at' => Carbon::parse($m->created_at)->translatedFormat('d F Y'),
            ];
        })->toArray();

        $user = Auth::user();
        $assignments = $class->assignments->map(function ($a) use ($user) {
            $mySubmission = $user ? $a->submissions->where('student_id', $user->id)->first() : null;
            return [
                'id' => $a->id,
                'title' => $a->title,
                'description' => $a->description ?? 'Instruksi tugas',
                'deadline' => Carbon::parse($a->deadline)->format('Y-m-d H:i'),
                'is_submitted' => !is_null($mySubmission),
                'score' => $mySubmission?->score,
                'feedback' => $mySubmission?->feedback,
                'submissions_count' => $a->submissions->count(),
            ];
        })->toArray();

        return view('academic.courses.show', compact('course', 'materials', 'assignments'));
    }

    /**
     * Show course materials tab.
     */
    public function materials($id)
    {
        $class = CourseClass::with(['course', 'lecturer', 'schedules.room', 'materials'])->findOrFail($id);
        $course = $this->formatCourseClass($class);

        $materials = $class->materials->map(function ($m) {
            return [
                'id' => $m->id,
                'title' => $m->title,
                'description' => $m->description ?? 'Modul materi perkuliahan',
                'file_name' => basename($m->file_path ?? 'Materi_Kuliah.pdf'),
                'file_size' => '2.4 MB',
                'uploaded_at' => Carbon::parse($m->created_at)->translatedFormat('d F Y'),
            ];
        })->toArray();

        return view('academic.courses.materials', compact('course', 'materials'));
    }

    /**
     * Add material (for lecturer).
     */
    public function addMaterial(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $class = CourseClass::findOrFail($id);

        Material::create([
            'course_class_id' => $class->id,
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'file_path' => 'materials/Materi_' . time() . '.pdf',
        ]);

        return redirect('/courses/' . $id . '/materials')->with('success', 'Materi perkuliahan berhasil ditambahkan.');
    }

    /**
     * Delete material (for lecturer).
     */
    public function deleteMaterial($id, $materialId)
    {
        $material = Material::where('course_class_id', $id)->findOrFail($materialId);
        $material->delete();

        return redirect('/courses/' . $id . '/materials')->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Show course assignments tab.
     */
    public function assignments($id)
    {
        $class = CourseClass::with(['course', 'lecturer', 'schedules.room', 'assignments.submissions'])->findOrFail($id);
        $course = $this->formatCourseClass($class);
        $user = Auth::user();

        $assignments = $class->assignments->map(function ($a) use ($user) {
            $mySubmission = $user ? $a->submissions->where('student_id', $user->id)->first() : null;
            return [
                'id' => $a->id,
                'title' => $a->title,
                'description' => $a->description ?? 'Instruksi tugas',
                'deadline' => Carbon::parse($a->deadline)->format('Y-m-d H:i'),
                'is_submitted' => !is_null($mySubmission),
                'score' => $mySubmission?->score,
                'feedback' => $mySubmission?->feedback,
                'submissions_count' => $a->submissions->count(),
            ];
        })->toArray();

        $userEmail = $user?->email ?? '';

        return view('academic.courses.assignments', compact('course', 'assignments', 'userEmail'));
    }

    /**
     * Add assignment (for lecturer).
     */
    public function addAssignment(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'required',
            'description' => 'nullable|string',
        ]);

        $class = CourseClass::with('course')->findOrFail($id);

        $assignment = Assignment::create([
            'course_class_id' => $class->id,
            'title' => $request->input('title'),
            'deadline' => Carbon::parse($request->input('deadline')),
            'description' => $request->input('description', ''),
            'file_path' => 'assignments/Brief_' . time() . '.pdf',
        ]);

        // Notify students about the new assignment
        $students = \App\Models\User::where('role', 'mahasiswa')->get();
        foreach ($students as $student) {
            NotificationController::createNotification(
                $student->id,
                'Tugas Baru: ' . $assignment->title,
                "Tugas baru untuk mata kuliah " . ($class->course?->name ?? 'Mata Kuliah') . " telah dipublikasikan.",
                'assignment_new',
                '/courses/' . $id . '/assignments'
            );
        }

        return redirect('/courses/' . $id . '/assignments')->with('success', 'Tugas baru berhasil diterbitkan.');
    }

    /**
     * Submit assignment (for student).
     */
    public function submitAssignment(Request $request, $id, $assignmentId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $class = CourseClass::with('course')->findOrFail($id);
        $assignment = Assignment::findOrFail($assignmentId);
        $fileName = 'submissions/Tugas_' . $user->name . '_' . time() . '.zip';

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignmentId,
                'student_id' => $user->id,
            ],
            [
                'file_path' => $fileName,
                'updated_at' => now(),
            ]
        );

        // Notify lecturer about the submission
        if ($class->lecturer_id) {
            NotificationController::createNotification(
                $class->lecturer_id,
                'Pengumpulan Tugas: ' . $assignment->title,
                "Mahasiswa {$user->name} telah mengumpulkan tugas untuk kelas " . ($class->course?->name ?? 'Mata Kuliah') . ".",
                'submission_new',
                '/courses/' . $id . '/assignments'
            );
        }

        return redirect('/courses/' . $id . '/assignments')->with('success', 'Tugas Anda berhasil dikumpulkan.');
    }

    /**
     * Show full academic schedule.
     */
    public function schedule(Request $request)
    {
        $selectedDay = $request->query('day', 'Senin');
        $allSchedules = Schedule::with(['courseClass.course', 'courseClass.lecturer', 'room.building'])->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $schedules = [];

        foreach ($days as $day) {
            $schedules[$day] = $allSchedules->where('day', $day)->map(function ($s) {
                return [
                    'id' => $s->id,
                    'code' => $s->courseClass?->course?->code ?? 'MK',
                    'course' => $s->courseClass?->course?->name ?? 'Mata Kuliah',
                    'lecturer' => $s->courseClass?->lecturer?->name ?? 'Dosen',
                    'class' => $s->courseClass?->name ?? 'Kelas',
                    'time' => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5),
                    'room' => ($s->room?->name ?? 'Ruangan') . ' (' . ($s->room?->code ?? '') . ')',
                    'mode' => strtoupper($s->mode),
                ];
            })->values()->toArray();
        }

        return view('academic.schedule', compact('schedules', 'selectedDay'));
    }
}
