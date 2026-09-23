<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        $role = session('user_role', 'student');

        return match ($role) {
            'admin' => redirect('/admin/dashboard'),
            'lecturer' => $this->lecturerDashboard(),
            default => $this->studentDashboard(),
        };
    }

    /**
     * Admin dashboard.
     */
    public function adminDashboard()
    {
        $stats = [
            ['label' => 'Total Mahasiswa', 'value' => 156, 'icon' => 'academic', 'color' => '#3d5af5'],
            ['label' => 'Total Dosen', 'value' => 24, 'icon' => 'users', 'color' => '#7c3aed'],
            ['label' => 'Total Ruangan', 'value' => 32, 'icon' => 'building', 'color' => '#0891b2'],
            ['label' => 'Reservasi Pending', 'value' => 5, 'icon' => 'clock', 'color' => '#f59e0b'],
            ['label' => 'Reservasi Hari Ini', 'value' => 3, 'icon' => 'calendar', 'color' => '#10b981'],
            ['label' => 'Laporan Baru', 'value' => 2, 'icon' => 'shield', 'color' => '#ef4444'],
            ['label' => 'Prioritas Tinggi', 'value' => 1, 'icon' => 'alert', 'color' => '#f97316'],
            ['label' => 'Laporan Darurat', 'value' => 0, 'icon' => 'urgent', 'color' => '#dc2626'],
        ];

        $pendingReservations = [
            ['id' => 'rsv-001', 'requester' => 'Andi Pratama', 'role' => 'Mahasiswa', 'room' => 'Lab Rekayasa Komputer', 'date' => '2026-09-24', 'time' => '13:00 - 15:00', 'course' => 'Praktikum Jaringan', 'class' => 'TK 5A', 'status' => 'PENDING'],
            ['id' => 'rsv-002', 'requester' => 'Citra Dewi', 'role' => 'Mahasiswa', 'room' => 'Lab Rekayasa Komputer', 'date' => '2026-09-24', 'time' => '13:00 - 15:00', 'course' => 'Rekayasa Perangkat Lunak', 'class' => 'TIM 5A', 'status' => 'PENDING'],
            ['id' => 'rsv-003', 'requester' => 'Dr. Budi Santoso', 'role' => 'Dosen', 'room' => 'Kelas 3B', 'date' => '2026-09-25', 'time' => '08:00 - 10:00', 'course' => 'Basis Data Lanjut', 'class' => 'TIM 5B', 'status' => 'PENDING'],
        ];

        $recentReports = [
            ['id' => 'rpt-001', 'category' => 'Intimidasi', 'date' => '2026-09-22', 'priority' => 'HIGH', 'status' => 'SUBMITTED'],
            ['id' => 'rpt-002', 'category' => 'Perundungan', 'date' => '2026-09-21', 'priority' => 'MEDIUM', 'status' => 'UNDER_REVIEW'],
            ['id' => 'rpt-003', 'category' => 'Pelecehan Verbal', 'date' => '2026-09-20', 'priority' => 'LOW', 'status' => 'IN_PROGRESS'],
        ];

        return view('dashboard.admin', compact('stats', 'pendingReservations', 'recentReports'));
    }

    /**
     * Student dashboard.
     */
    private function studentDashboard()
    {
        $todaySchedule = [
            ['time' => '08:00 - 10:00', 'course' => 'Pemrograman Web', 'room' => 'Lab Multimedia', 'class' => 'TIM 5A', 'building' => 'Gedung TI', 'mode' => 'ONSITE'],
            ['time' => '10:00 - 12:00', 'course' => 'Basis Data Lanjut', 'room' => 'Kelas 2A', 'class' => 'TIM 5A', 'building' => 'Gedung TI', 'mode' => 'ONSITE'],
            ['time' => '13:00 - 15:00', 'course' => 'Rekayasa Perangkat Lunak', 'room' => '—', 'class' => 'TIM 5A', 'building' => '—', 'mode' => 'ONLINE'],
        ];

        $pendingAssignments = [
            ['title' => 'Tugas REST API - Laravel', 'course' => 'Pemrograman Web', 'deadline' => '2026-09-25', 'urgency' => 'high'],
            ['title' => 'ER Diagram Sistem Perpustakaan', 'course' => 'Basis Data Lanjut', 'deadline' => '2026-09-27', 'urgency' => 'medium'],
            ['title' => 'Dokumen SRS', 'course' => 'Rekayasa Perangkat Lunak', 'deadline' => '2026-10-01', 'urgency' => 'low'],
        ];

        $activeCourses = [
            ['id' => 'crs-001', 'code' => 'TI-401', 'name' => 'Pemrograman Web', 'lecturer' => 'Dr. Budi Santoso', 'class' => 'TIM 5A'],
            ['id' => 'crs-002', 'code' => 'TI-402', 'name' => 'Basis Data Lanjut', 'lecturer' => 'Siti Aminah, M.Kom', 'class' => 'TIM 5A'],
            ['id' => 'crs-003', 'code' => 'TI-403', 'name' => 'Rekayasa Perangkat Lunak', 'lecturer' => 'Dr. Budi Santoso', 'class' => 'TIM 5A'],
        ];

        $recentReservations = [
            ['id' => 'rsv-001', 'room' => 'Lab Rekayasa Komputer', 'date' => '2026-09-24', 'time' => '13:00 - 15:00', 'status' => 'PENDING'],
        ];

        return view('dashboard.student', compact('todaySchedule', 'pendingAssignments', 'activeCourses', 'recentReservations'));
    }

    /**
     * Lecturer dashboard.
     */
    private function lecturerDashboard()
    {
        $todaySchedule = [
            ['time' => '08:00 - 10:00', 'course' => 'Pemrograman Web', 'room' => 'Lab Multimedia', 'class' => 'TIM 5A', 'building' => 'Gedung TI', 'mode' => 'ONSITE'],
            ['time' => '10:00 - 12:00', 'course' => 'Pemrograman Web', 'room' => 'Lab Multimedia', 'class' => 'TIM 5B', 'building' => 'Gedung TI', 'mode' => 'ONSITE'],
            ['time' => '13:00 - 15:00', 'course' => 'Rekayasa Perangkat Lunak', 'room' => '—', 'class' => 'TIM 5A', 'building' => '—', 'mode' => 'ONLINE'],
        ];

        $taughtCourses = [
            ['id' => 'crs-001', 'code' => 'TI-401', 'name' => 'Pemrograman Web', 'classes' => ['TIM 5A', 'TIM 5B'], 'students' => 62],
            ['id' => 'crs-003', 'code' => 'TI-403', 'name' => 'Rekayasa Perangkat Lunak', 'classes' => ['TIM 5A'], 'students' => 30],
        ];

        $recentSubmissions = [
            ['student' => 'Andi Pratama', 'assignment' => 'Tugas REST API', 'course' => 'Pemrograman Web', 'submitted' => '2026-09-22 14:30'],
            ['student' => 'Citra Dewi', 'assignment' => 'Tugas REST API', 'course' => 'Pemrograman Web', 'submitted' => '2026-09-22 15:45'],
            ['student' => 'Eka Putra', 'assignment' => 'Dokumen SRS', 'course' => 'Rekayasa Perangkat Lunak', 'submitted' => '2026-09-21 20:10'],
        ];

        return view('dashboard.lecturer', compact('todaySchedule', 'taughtCourses', 'recentSubmissions'));
    }
}
