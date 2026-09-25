<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * User's reports list (/reports).
     */
    public function index(Request $request)
    {
        $all = MockDataService::getReports();
        $userEmail = session('user_email', 'student@sakala.test');
        $userRole = session('user_role', 'student');

        // Only show reporter's reports unless admin
        if ($userRole !== 'admin') {
            $reports = array_filter($all, fn($r) => $r['reporter_email'] === $userEmail);
        } else {
            $reports = $all;
        }

        $categoryFilter = $request->query('category', 'all');
        if ($categoryFilter !== 'all') {
            $reports = array_filter($reports, fn($r) => strtolower($r['category']) === strtolower($categoryFilter));
        }

        return view('reports.index', compact('reports', 'categoryFilter'));
    }

    /**
     * Create report form (/reports/create).
     */
    public function create()
    {
        return view('reports.create');
    }

    /**
     * Store new report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'incident_date' => 'required|date',
            'location' => 'required|string',
            'description' => 'required|string',
        ]);

        $report = MockDataService::createReport($request->all());

        return redirect('/reports/' . $report['id'])->with('success', 'Laporan Anda telah berhasil dikirim dengan aman dan privasi terjamin.');
    }

    /**
     * View report detail & timeline (/reports/{id}).
     */
    public function show($id)
    {
        $reports = MockDataService::getReports();
        $report = collect($reports)->firstWhere('id', $id);

        if (!$report) {
            abort(404, 'Laporan tidak ditemukan');
        }

        // Privacy check
        $userEmail = session('user_email', 'student@sakala.test');
        $userRole = session('user_role', 'student');
        if ($userRole !== 'admin' && $report['reporter_email'] !== $userEmail) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat laporan ini.');
        }

        return view('reports.show', compact('report'));
    }

    /**
     * Admin reports management (/admin/reports).
     */
    public function adminIndex(Request $request)
    {
        $all = MockDataService::getReports();
        $statusFilter = $request->query('status', 'all');
        $priorityFilter = $request->query('priority', 'all');

        $reports = $all;

        if ($statusFilter !== 'all') {
            $reports = array_filter($reports, fn($r) => strtolower($r['status']) === strtolower($statusFilter));
        }

        if ($priorityFilter !== 'all') {
            $reports = array_filter($reports, fn($r) => strtolower($r['priority']) === strtolower($priorityFilter));
        }

        $urgentCount = count(array_filter($all, fn($r) => $r['priority'] === 'URGENT'));
        $highCount = count(array_filter($all, fn($r) => $r['priority'] === 'HIGH'));
        $inProgressCount = count(array_filter($all, fn($r) => $r['status'] === 'IN_PROGRESS'));
        $submittedCount = count(array_filter($all, fn($r) => $r['status'] === 'SUBMITTED'));

        return view('admin.reports.index', compact(
            'reports', 
            'statusFilter', 
            'priorityFilter',
            'urgentCount',
            'highCount',
            'inProgressCount',
            'submittedCount'
        ));
    }

    /**
     * Admin update report status & priority (/admin/reports/{id}/update).
     */
    public function adminUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'priority' => 'required|string',
        ]);

        MockDataService::updateReport(
            $id, 
            $request->input('status'), 
            $request->input('priority'), 
            $request->input('admin_note')
        );

        return redirect()->back()->with('success', 'Status & tindak lanjut laporan berhasil diperbarui.');
    }
}
