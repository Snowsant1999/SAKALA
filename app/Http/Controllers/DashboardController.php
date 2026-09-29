<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseClass;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ReservationConflictDetector;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect('/login');
        }

        return match ($user->role) {
            'admin' => redirect('/admin/dashboard'),
            'dosen' => $this->lecturerDashboard(),
            default => $this->studentDashboard(),
        };
    }

    /**
     * Admin dashboard.
     */
    public function adminDashboard(ReservationConflictDetector $conflictDetector)
    {
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalRuangan = Room::count();
        $reservasiPending = Reservation::where('status', 'pending')->count();
        $reservasiHariIni = Reservation::whereDate('date', Carbon::today())->count();
        $laporanBaru = Report::where('status', 'pending')->count();
        $laporanPrioritasTinggi = Report::where('priority', 'high')->count();
        $laporanDarurat = Report::where('priority', 'urgent')->count();
        $aspirasiBaru = Aspiration::where('status', 'pending')->count();
        $conflictCount = count($conflictDetector->groups(
            Reservation::query()
                ->whereIn('status', ['pending', 'approved'])
                ->get(['room_id', 'date', 'start_time', 'end_time', 'status']),
        ));

        $stats = [
            ['label' => 'Total Mahasiswa', 'value' => $totalMahasiswa, 'icon' => 'academic', 'color' => '#3d5af5', 'url' => '/admin/students'],
            ['label' => 'Total Dosen', 'value' => $totalDosen, 'icon' => 'users', 'color' => '#7c3aed', 'url' => '/admin/lecturers'],
            ['label' => 'Total Ruangan', 'value' => $totalRuangan, 'icon' => 'building', 'color' => '#0891b2', 'url' => '/admin/rooms'],
            ['label' => 'Reservasi Pending', 'value' => $reservasiPending, 'icon' => 'clock', 'color' => '#f59e0b', 'url' => '/admin/reservations?tab=pending'],
            ['label' => 'Reservasi Hari Ini', 'value' => $reservasiHariIni, 'icon' => 'calendar', 'color' => '#10b981', 'url' => '/admin/reservations?tab=today'],
            ['label' => 'Laporan Baru', 'value' => $laporanBaru, 'icon' => 'shield', 'color' => '#0891b2', 'url' => '/admin/reports?status=submitted'],
            ['label' => 'Prioritas Tinggi', 'value' => $laporanPrioritasTinggi, 'icon' => 'alert', 'color' => '#f97316', 'url' => '/admin/reports?priority=high'],
            ['label' => 'Laporan Darurat', 'value' => $laporanDarurat, 'icon' => 'alert', 'color' => '#dc2626', 'url' => '/admin/reports?priority=urgent'],
            ['label' => 'Aspirasi Masuk', 'value' => $aspirasiBaru, 'icon' => 'chat', 'color' => '#8b5cf6', 'url' => '/admin/aspirations?status=pending'],
        ];

        $pendingReservations = Reservation::with(['user.studyProgram', 'room.building', 'courseClass'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($rsv) {
                return [
                    'id' => $rsv->id,
                    'requester' => $rsv->user?->name ?? 'Anonim',
                    'role' => ucfirst($rsv->user?->role ?? 'Mahasiswa'),
                    'class' => $rsv->courseClass?->name ?? 'Umum',
                    'room' => ($rsv->room?->name ?? 'Ruangan').' ('.($rsv->room?->code ?? '').')',
                    'date' => Carbon::parse($rsv->date)->format('Y-m-d'),
                    'time' => substr($rsv->start_time, 0, 5).' - '.substr($rsv->end_time, 0, 5),
                    'purpose' => $rsv->purpose,
                    'status' => strtoupper($rsv->status),
                ];
            })->toArray();

        $recentReports = Report::with('reporter')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($rpt) {
                return [
                    'id' => 'RPT-'.str_pad((string) $rpt->id, 4, '0', STR_PAD_LEFT),
                    'raw_id' => $rpt->id,
                    'category' => $rpt->category,
                    'date' => Carbon::parse($rpt->incident_date)->format('Y-m-d'),
                    'priority' => strtoupper($rpt->priority ?? 'medium'),
                    'status' => match ($rpt->status) {
                        'under_review' => 'UNDER_REVIEW',
                        'in_progress' => 'IN_PROGRESS',
                        'resolved' => 'RESOLVED',
                        'rejected', 'dismissed' => 'REJECTED',
                        default => 'SUBMITTED',
                    },
                ];
            })->toArray();

        return view('dashboard.admin', compact('stats', 'pendingReservations', 'recentReports', 'conflictCount'));
    }

    /**
     * Student dashboard.
     */
    private function studentDashboard()
    {
        $user = Auth::user();
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $todayIndo = $days[Carbon::now()->dayOfWeek] ?? 'Senin';

        $schedulesQuery = Schedule::with(['courseClass.course', 'room.building'])
            ->where('day', $todayIndo)
            ->orderBy('start_time', 'asc');

        if ($user->cohort_id === null) {
            $schedulesQuery->whereRaw('1 = 0');
        } else {
            $schedulesQuery->whereHas('courseClass', fn ($query) => $query->where('cohort_id', $user->cohort_id));
        }

        $schedulesQuery = $schedulesQuery->get();

        $todaySchedule = $schedulesQuery->map(function ($sch) {
            return [
                'time' => substr($sch->start_time, 0, 5).' - '.substr($sch->end_time, 0, 5),
                'course' => $sch->courseClass?->course?->name ?? 'Mata Kuliah',
                'room' => $sch->room?->name ?? 'Online',
                'class' => $sch->courseClass?->name ?? 'Kelas',
                'building' => $sch->room?->building?->name ?? '—',
                'mode' => strtoupper($sch->mode),
            ];
        })->toArray();

        // Pending assignments
        $pendingAssignmentsQuery = Assignment::with('courseClass.course')
            ->whereDoesntHave('submissions', fn ($query) => $query->where('student_id', $user->id))
            ->where('deadline', '>=', Carbon::now())
            ->orderBy('deadline', 'asc');

        if ($user->cohort_id === null) {
            $pendingAssignmentsQuery->whereRaw('1 = 0');
        } else {
            $pendingAssignmentsQuery->whereHas('courseClass', fn ($query) => $query->where('cohort_id', $user->cohort_id));
        }

        $pendingAssignments = $pendingAssignmentsQuery->take(5)->get()
            ->map(function ($assign) {
                $daysDiff = Carbon::now()->diffInDays(Carbon::parse($assign->deadline), false);
                $urgency = $daysDiff <= 2 ? 'high' : ($daysDiff <= 5 ? 'medium' : 'low');

                return [
                    'id' => $assign->id,
                    'title' => $assign->title,
                    'course' => $assign->courseClass?->course?->name ?? 'Mata Kuliah',
                    'deadline' => Carbon::parse($assign->deadline)->format('Y-m-d H:i'),
                    'urgency' => $urgency,
                ];
            })->toArray();

        // Active courses
        $activeCoursesQuery = CourseClass::with(['course', 'lecturer'])->where('cohort_id', $user->cohort_id);
        if ($user->cohort_id === null) {
            $activeCoursesQuery->whereRaw('1 = 0');
        }

        $activeCourses = $activeCoursesQuery
            ->get()
            ->map(function ($cls) {
                return [
                    'id' => $cls->id,
                    'code' => $cls->course?->code ?? 'MK',
                    'name' => $cls->course?->name ?? 'Mata Kuliah',
                    'lecturer' => $cls->lecturer?->name ?? 'Dosen Pengampu',
                    'class' => $cls->name,
                ];
            })->toArray();

        // User recent reservations
        $recentReservations = Reservation::with('room')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($rsv) {
                return [
                    'id' => $rsv->id,
                    'room' => $rsv->room?->name ?? 'Ruangan',
                    'date' => Carbon::parse($rsv->date)->format('Y-m-d'),
                    'time' => substr($rsv->start_time, 0, 5).' - '.substr($rsv->end_time, 0, 5),
                    'status' => strtoupper($rsv->status),
                ];
            })->toArray();

        return view('dashboard.student', compact('todaySchedule', 'pendingAssignments', 'activeCourses', 'recentReservations'));
    }

    /**
     * Lecturer dashboard.
     */
    private function lecturerDashboard()
    {
        $user = Auth::user();
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $todayIndo = $days[Carbon::now()->dayOfWeek] ?? 'Senin';

        // Lecturer's schedule today
        $schedulesQuery = Schedule::whereHas('courseClass', function ($q) use ($user) {
            $q->where('lecturer_id', $user->id);
        })->with(['courseClass.course', 'room.building'])
            ->where('day', $todayIndo)
            ->orderBy('start_time', 'asc')
            ->get();

        if ($schedulesQuery->isEmpty()) {
            $schedulesQuery = Schedule::whereHas('courseClass', function ($q) use ($user) {
                $q->where('lecturer_id', $user->id);
            })->with(['courseClass.course', 'room.building'])
                ->orderBy('day', 'asc')
                ->orderBy('start_time', 'asc')
                ->take(3)
                ->get();
        }

        $todaySchedule = $schedulesQuery->map(function ($sch) {
            return [
                'time' => substr($sch->start_time, 0, 5).' - '.substr($sch->end_time, 0, 5),
                'course' => $sch->courseClass?->course?->name ?? 'Mata Kuliah',
                'room' => $sch->room?->name ?? 'Online',
                'class' => $sch->courseClass?->name ?? 'Kelas',
                'building' => $sch->room?->building?->name ?? '—',
                'mode' => strtoupper($sch->mode),
            ];
        })->toArray();

        // Taught courses
        $taughtCourses = CourseClass::where('lecturer_id', $user->id)
            ->with('course')
            ->get()
            ->groupBy('course_id')
            ->map(function ($classes) {
                $first = $classes->first();

                return [
                    'id' => $first->id,
                    'code' => $first->course?->code ?? 'MK',
                    'name' => $first->course?->name ?? 'Mata Kuliah',
                    'classes' => $classes->pluck('name')->toArray(),
                    'students' => 30 * count($classes),
                ];
            })->values()->toArray();

        // Recent submissions for assignments in lecturer's classes
        $recentSubmissions = AssignmentSubmission::whereHas('assignment.courseClass', function ($q) use ($user) {
            $q->where('lecturer_id', $user->id);
        })->with(['student', 'assignment.courseClass.course'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($sub) {
                return [
                    'student' => $sub->student?->name ?? 'Mahasiswa',
                    'assignment' => $sub->assignment?->title ?? 'Tugas',
                    'course' => $sub->assignment?->courseClass?->course?->name ?? 'Mata Kuliah',
                    'submitted' => Carbon::parse($sub->created_at)->format('Y-m-d H:i'),
                ];
            })->toArray();

        return view('dashboard.lecturer', compact('todaySchedule', 'taughtCourses', 'recentSubmissions'));
    }
}
