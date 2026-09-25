<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * User's reservations page (/reservations).
     */
    public function index(Request $request)
    {
        $all = MockDataService::getReservations();
        $userEmail = session('user_email', 'student@sakala.test');
        $userRole = session('user_role', 'student');

        // Filter by user unless admin
        if ($userRole !== 'admin') {
            $reservations = array_filter($all, fn($r) => $r['requester_email'] === $userEmail);
        } else {
            $reservations = $all;
        }

        // Filter by status tab
        $statusFilter = $request->query('status', 'all');
        if ($statusFilter !== 'all') {
            $reservations = array_filter($reservations, fn($r) => strtolower($r['status']) === strtolower($statusFilter));
        }

        return view('reservations.index', compact('reservations', 'statusFilter'));
    }

    /**
     * Create reservation form (/reservations/create).
     */
    public function create(Request $request)
    {
        $roomId = $request->query('room_id', 'rm-004');
        $date = $request->query('date', date('Y-m-d', strtotime('+1 day')));
        $startTime = $request->query('start_time', '13:00');
        $endTime = $request->query('end_time', '15:00');

        $courses = MockDataService::getCourses();

        return view('reservations.create', compact('roomId', 'date', 'startTime', 'endTime', 'courses'));
    }

    /**
     * Store new reservation request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_name' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'purpose' => 'required|string',
        ]);

        $newReservation = MockDataService::createReservation($request->all());

        return redirect('/reservations')->with('success', 'Request reservasi berhasil diajukan dan sedang menunggu persetujuan admin.');
    }

    /**
     * Show reservation detail (/reservations/{id}).
     */
    public function show($id)
    {
        $reservations = MockDataService::getReservations();
        $reservation = collect($reservations)->firstWhere('id', $id);

        if (!$reservation) {
            abort(404, 'Reservasi tidak ditemukan');
        }

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Admin reservations management (/admin/reservations).
     */
    public function adminIndex(Request $request)
    {
        $all = MockDataService::getReservations();
        $conflicts = MockDataService::getConflicts();

        $activeTab = $request->query('tab', 'pending');

        if ($activeTab === 'conflicts') {
            $reservations = [];
        } elseif ($activeTab === 'all') {
            $reservations = $all;
        } else {
            $reservations = array_filter($all, fn($r) => strtolower($r['status']) === strtolower($activeTab));
        }

        $pendingCount = count(array_filter($all, fn($r) => $r['status'] === 'PENDING'));
        $approvedCount = count(array_filter($all, fn($r) => $r['status'] === 'APPROVED'));
        $rejectedCount = count(array_filter($all, fn($r) => $r['status'] === 'REJECTED'));
        $conflictCount = count($conflicts);

        return view('admin.reservations.index', compact(
            'reservations', 
            'conflicts', 
            'activeTab', 
            'pendingCount', 
            'approvedCount', 
            'rejectedCount', 
            'conflictCount'
        ));
    }

    /**
     * Admin approve reservation (/admin/reservations/{id}/approve).
     */
    public function adminApprove(Request $request, $id)
    {
        $adminNote = $request->input('admin_note', 'Disetujui oleh Admin Akademik SAKALA.');
        MockDataService::approveReservation($id, $adminNote);

        return redirect()->back()->with('success', 'Reservasi berhasil DISETUJUI. Jika ada request lain yang bentrok, telah otomatis ditolak oleh sistem.');
    }

    /**
     * Admin reject reservation (/admin/reservations/{id}/reject).
     */
    public function adminReject(Request $request, $id)
    {
        $adminNote = $request->input('admin_note', 'Ditolak: Ruangan tidak dapat digunakan pada waktu yang diminta.');
        MockDataService::rejectReservation($id, $adminNote);

        return redirect()->back()->with('success', 'Reservasi berhasil DITOLAK.');
    }
}
