<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Format Report model for view compatibility.
     */
    private function formatReport(Report $r): array
    {
        $viewStatus = match ($r->status) {
            'pending' => 'SUBMITTED',
            'investigating' => 'IN_PROGRESS',
            'resolved' => 'RESOLVED',
            default => 'UNDER_REVIEW',
        };

        $timeline = [
            [
                'time' => Carbon::parse($r->created_at)->translatedFormat('d M Y, H:i'),
                'title' => 'Laporan Masuk & Tercatat',
                'description' => 'Laporan berhasil didaftarkan secara terenkripsi ke dalam sistem SAKALA.',
                'status' => 'completed',
            ],
        ];

        if ($r->status === 'investigating' || $r->status === 'resolved') {
            $timeline[] = [
                'time' => Carbon::parse($r->updated_at)->translatedFormat('d M Y, H:i'),
                'title' => 'Dalam Penyelidikan Satgas PPKS',
                'description' => $r->admin_notes ?: 'Tim Satgas PPKS telah memverifikasi bukti awal dan memproses tindak lanjut.',
                'status' => 'completed',
            ];
        }

        if ($r->status === 'resolved') {
            $timeline[] = [
                'time' => Carbon::parse($r->updated_at)->translatedFormat('d M Y, H:i'),
                'title' => 'Selesai Ditangani',
                'description' => 'Laporan telah diselesaikan dan kesepakatan pemulihan/sanksi telah disepakati.',
                'status' => 'completed',
            ];
        }

        return [
            'id' => 'RPT-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
            'raw_id' => $r->id,
            'category' => $r->category,
            'incident_date' => Carbon::parse($r->incident_date)->format('Y-m-d'),
            'location' => $r->location,
            'involved_parties' => $r->involved_parties ?? 'Tidak disebutkan',
            'description' => $r->description,
            'status' => $viewStatus,
            'priority' => 'HIGH',
            'admin_note' => $r->admin_notes,
            'attachments' => $r->attachment_path ? basename($r->attachment_path) : 'Tidak ada lampiran',
            'reporter_name' => $r->reporter?->name ?? 'Anonim',
            'reporter_email' => $r->reporter?->email ?? '',
            'reporter_nim' => $r->reporter?->nim_nip ?? '—',
            'reporter_role' => ucfirst($r->reporter?->role ?? 'Mahasiswa'),
            'created_at' => Carbon::parse($r->created_at)->translatedFormat('d F Y, H:i'),
            'timeline' => $timeline,
        ];
    }

    /**
     * User's reports list (/reports).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $query = Report::with('reporter')->latest();

        // Only show reporter's reports unless admin
        if ($user->role !== 'admin') {
            $query->where('reporter_id', $user->id);
        }

        $categoryFilter = $request->query('category', 'all');
        if ($categoryFilter !== 'all') {
            $query->where('category', 'like', "%{$categoryFilter}%");
        }

        $reports = $query->get()->map(fn($r) => $this->formatReport($r))->toArray();

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

        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $report = Report::create([
            'reporter_id' => $user->id,
            'category' => $request->input('category'),
            'incident_date' => $request->input('incident_date'),
            'location' => $request->input('location'),
            'involved_parties' => $request->input('involved_parties'),
            'description' => $request->input('description'),
            'attachment_path' => null,
            'status' => 'pending',
        ]);

        // Notify admins about the new report
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            NotificationController::createNotification(
                $admin->id,
                'Laporan Kampus Aman Baru',
                "Laporan baru kategori '{$report->category}' di {$report->location} telah masuk.",
                'report_new',
                '/admin/reports'
            );
        }

        return redirect('/reports/' . $report->id)->with('success', 'Laporan Anda telah berhasil dikirim dengan aman dan privasi terjamin.');
    }

    /**
     * View report detail & timeline (/reports/{id}).
     */
    public function show($id)
    {
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $reportModel = Report::with('reporter')->findOrFail($numericId);
        $user = Auth::user();

        // Privacy check
        if ($user->role !== 'admin' && $reportModel->reporter_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat laporan ini.');
        }

        $report = $this->formatReport($reportModel);

        return view('reports.show', compact('report'));
    }

    /**
     * Admin reports management (/admin/reports).
     */
    public function adminIndex(Request $request)
    {
        $query = Report::with('reporter')->latest();
        $statusFilter = $request->query('status', 'all');
        $priorityFilter = $request->query('priority', 'all');

        if ($statusFilter !== 'all') {
            $dbStatus = match (strtolower($statusFilter)) {
                'submitted' => 'pending',
                'in_progress' => 'investigating',
                'resolved' => 'resolved',
                default => $statusFilter,
            };
            $query->where('status', $dbStatus);
        }

        $allReports = Report::all();
        $urgentCount = 1;
        $highCount = $allReports->where('status', '!=', 'resolved')->count();
        $inProgressCount = $allReports->where('status', 'investigating')->count();
        $submittedCount = $allReports->where('status', 'pending')->count();

        $reports = $query->get()->map(fn($r) => $this->formatReport($r))->toArray();

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
        ]);

        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $report = Report::findOrFail($numericId);

        $dbStatus = match (strtoupper($request->input('status'))) {
            'SUBMITTED' => 'pending',
            'UNDER_REVIEW', 'IN_PROGRESS' => 'investigating',
            'RESOLVED' => 'resolved',
            'DISMISSED' => 'dismissed',
            default => 'pending',
        };

        $report->update([
            'status' => $dbStatus,
            'admin_notes' => $request->input('admin_note', $report->admin_notes),
        ]);

        if ($report->reporter_id) {
            $statusLabel = match($dbStatus) {
                'investigating' => 'Sedang Diinvestigasi',
                'resolved' => 'Telah Selesai Ditangani',
                'dismissed' => 'Ditolak / Diarsipkan',
                default => 'Diterima',
            };

            NotificationController::createNotification(
                $report->reporter_id,
                'Update Laporan Kampus Aman',
                "Laporan RPT-" . str_pad($report->id, 4, '0', STR_PAD_LEFT) . " ({$report->category}) kini berstatus: {$statusLabel}.",
                'report_status',
                '/reports/' . $report->id
            );
        }

        return redirect()->back()->with('success', 'Status & tindak lanjut laporan berhasil diperbarui.');
    }
}
