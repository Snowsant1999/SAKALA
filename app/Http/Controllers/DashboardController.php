<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Report;
use App\Models\Aspiration;
use App\Models\Schedule;
use App\Models\Assignment;
use App\Models\CourseClass;
use App\Models\AssignmentSubmission;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
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
    public function adminDashboard()
    {
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalRuangan = Room::count();
        $reservasiPending = Reservation::where('status', 'pending')->count();
        $reservasiHariIni = Reservation::whereDate('date', Carbon::today())->count();
        $laporanBaru = Report::where('status', 'pending')->count();
        $aspirasiBaru = Aspiration::where('status', 'pending')->count();

        $stats = [
            ['label' => 'Total Mahasiswa', 'value' => $totalMahasiswa, 'icon' => 'academic', 'color' => '#3d5af5'],
            ['label' => 'Total Dosen', 'value' => $totalDosen, 'icon' => 'users', 'color' => '#7c3aed'],
            ['label' => 'Total Ruangan', 'value' => $totalRuangan, 'icon' => 'building', 'color' => '#0891b2'],
            ['label' => 'Reservasi Pending', 'value' => $reservasiPending, 'icon' => 'clock', 'color' => '#f59e0b'],
            ['label' => 'Reservasi Hari Ini', 'value' => $reservasiHariIni, 'icon' => 'calendar', 'color' => '#10b981'],
            ['label' => 'Laporan Aman Baru', 'value' => $laporanBaru, 'icon' => 'shield', 'color' => '#ef4444'],
            ['label' => 'Aspirasi Masuk', 'value' => $aspirasiBaru, 'icon' => 'chat', 'color' => '#8b5cf6'],
        ];

        $pendingReservations = Reservation::with(['user.studyProgram', 'room.building'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($rsv) {
                return [
                    'id' => $rsv->id,
                    'requester' => $rsv->user?->name ?? 'Anonim',
                    'role' => ucfirst($rsv->user?->role ?? 'Mahasiswa'),
                    'class' => $rsv->user?->studyProgram?->name ?? 'Umum',
                    'room' => ($rsv->room?->name ?? 'Ruangan') . ' (' . ($rsv->room?->code ?? '') . ')',
                    'date' => Carbon::parse($rsv->date)->format('Y-m-d'),
                    'time' => substr($rsv->start_time, 0, 5) . ' - ' . substr($rsv->end_time, 0, 5),
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
                    'id' => $rpt->id,
                    'category' => $rpt->category,
                    'date' => Carbon::parse($rpt->incident_date)->format('Y-m-d'),
                    'priority' => 'HIGH',
                    'status' => strtoupper($rpt->status),
                ];
            })->toArray();

        return view('dashboard.admin', compact('stats', 'pendingReservations', 'recentReports'));
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

        // Schedules for today (or fallback to all active schedules if none today)
        $schedulesQuery = Schedule::with(['courseClass.course', 'room.building'])
            ->where('day', $todayIndo)
            ->orderBy('start_time', 'asc')
            ->get();

        if ($schedulesQuery->isEmpty()) {
            // If weekend or no schedule today, show upcoming schedules
            $schedulesQuery = Schedule::with(['courseClass.course', 'room.building'])
                ->orderBy('day', 'asc')
                ->orderBy('start_time', 'asc')
                ->take(3)
                ->get();
        }

        $todaySchedule = $schedulesQuery->map(function ($sch) {
            return [
                'time' => substr($sch->start_time, 0, 5) . ' - ' . substr($sch->end_time, 0, 5),
                'course' => $sch->courseClass?->course?->name ?? 'Mata Kuliah',
                'room' => $sch->room?->name ?? 'Online',
                'class' => $sch->courseClass?->name ?? 'Kelas',
                'building' => $sch->room?->building?->name ?? '—',
                'mode' => strtoupper($sch->mode),
            ];
        })->toArray();

        // Pending assignments
        $pendingAssignments = Assignment::with('courseClass.course')
            ->where('deadline', '>=', Carbon::now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get()
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
        $activeCourses = CourseClass::with(['course', 'lecturer'])
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
                    'time' => substr($rsv->start_time, 0, 5) . ' - ' . substr($rsv->end_time, 0, 5),
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
                'time' => substr($sch->start_time, 0, 5) . ' - ' . substr($sch->end_time, 0, 5),
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
