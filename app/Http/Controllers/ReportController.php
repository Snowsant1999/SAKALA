<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportStatusHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Format Report model for view compatibility.
     */
    private function formatReport(Report $r, bool $showReporterIdentity = false): array
    {
        $viewStatus = match ($r->status) {
            'pending' => 'SUBMITTED',
            'under_review' => 'UNDER_REVIEW',
            'in_progress', 'investigating' => 'IN_PROGRESS',
            'resolved' => 'RESOLVED',
            'rejected', 'dismissed' => 'REJECTED',
            default => 'SUBMITTED',
        };

        $timeline = $r->statusHistories
            ->sortBy('created_at')
            ->map(function (ReportStatusHistory $history): array {
                $statusLabel = match ($history->to_status) {
                    'pending' => 'Laporan Masuk & Tercatat',
                    'under_review' => 'Laporan Sedang Ditelaah',
                    'in_progress', 'investigating' => 'Dalam Penanganan',
                    'resolved' => 'Selesai Ditangani',
                    'rejected', 'dismissed' => 'Laporan Diarsipkan',
                    default => 'Status Laporan Diperbarui',
                };

                $priorityLabel = $history->to_priority
                    ? ' Prioritas: '.strtoupper($history->to_priority).'.'
                    : '';

                return [
                    'time' => $history->created_at->translatedFormat('d M Y, H:i'),
                    'title' => $statusLabel,
                    'desc' => ($history->admin_note ?: 'Pembaruan laporan tercatat.').$priorityLabel,
                    'status' => 'completed',
                ];
            })
            ->values()
            ->all();

        if ($timeline === []) {
            $timeline[] = [
                'time' => Carbon::parse($r->created_at)->translatedFormat('d M Y, H:i'),
                'title' => 'Laporan Masuk & Tercatat',
                'desc' => 'Laporan berhasil didaftarkan secara rahasia ke dalam sistem SAKALA.',
                'status' => 'completed',
            ];
        }

        return [
            'id' => 'RPT-'.str_pad($r->id, 4, '0', STR_PAD_LEFT),
            'raw_id' => $r->id,
            'category' => $r->category,
            'incident_date' => Carbon::parse($r->incident_date)->format('Y-m-d'),
            'date' => Carbon::parse($r->incident_date)->format('Y-m-d'),
            'location' => $r->location,
            'involved_parties' => $r->involved_parties ?? 'Tidak disebutkan',
            'description' => $r->description,
            'status' => $viewStatus,
            'priority' => strtoupper($r->priority ?? 'medium'),
            'admin_note' => $r->admin_notes,
            'attachments' => $r->attachment_path ? basename($r->attachment_path) : 'Tidak ada lampiran',
            'has_attachment' => $r->attachment_path !== null && $r->attachment_path !== '',
            'reporter_name' => $showReporterIdentity
                ? ($r->reporter?->name ?? 'Anonim')
                : $this->reporterInitials($r->reporter?->name),
            'reporter_email' => $showReporterIdentity ? ($r->reporter?->email ?? '') : '',
            'reporter_nim' => $showReporterIdentity ? ($r->reporter?->nim_nip ?? '—') : '—',
            'reporter_role' => ucfirst($r->reporter?->role ?? 'Mahasiswa'),
            'created_at' => Carbon::parse($r->created_at)->translatedFormat('d F Y, H:i'),
            'timeline' => $timeline,
        ];
    }

    private function reporterInitials(?string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name ?? ''), -1, PREG_SPLIT_NO_EMPTY);

        if (! $parts) {
            return 'Anonim';
        }

        return collect(array_slice($parts, 0, 2))
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)).'.')
            ->implode(' ');
    }

    /**
     * User's reports list (/reports).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
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

        $reports = $query->get()->map(fn ($r) => $this->formatReport($r))->toArray();

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
        $data = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'location' => ['required', 'string', 'max:255'],
            'involved_parties' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:20000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,mp3,mp4', 'max:10240'],
        ]);

        $user = Auth::user();
        if (! $user) {
            return redirect('/login');
        }

        $attachmentPath = $request->file('attachment')?->store('report-attachments', 'local');
        $report = Report::create([
            'reporter_id' => $user->id,
            'category' => $data['category'],
            'incident_date' => $data['incident_date'],
            'location' => $data['location'],
            'involved_parties' => $data['involved_parties'] ?? null,
            'description' => $data['description'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        ReportStatusHistory::create([
            'report_id' => $report->id,
            'from_status' => null,
            'to_status' => 'pending',
            'from_priority' => null,
            'to_priority' => 'medium',
            'admin_note' => 'Laporan diterima oleh sistem.',
        ]);

        // Notify admins about the new report
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            NotificationController::createNotification(
                $admin->id,
                'Laporan Kampus Aman Baru',
                "Laporan baru kategori '{$report->category}' di {$report->location} telah masuk.",
                'report_new',
                '/admin/reports'
            );
        }

        return redirect('/reports/'.$report->id)->with('success', 'Laporan berhasil dikirim. Nama lengkap Anda hanya dapat dilihat oleh admin SAKALA.');
    }

    /**
     * View report detail & timeline (/reports/{id}).
     */
    public function show($id)
    {
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $reportModel = Report::with(['reporter', 'statusHistories.admin'])->findOrFail($numericId);
        $user = Auth::user();

        // Privacy check
        if ($user->role !== 'admin' && $reportModel->reporter_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat laporan ini.');
        }

        $report = $this->formatReport($reportModel, $user->role === 'admin');

        return view('reports.show', compact('report'));
    }

    public function downloadAttachment($id)
    {
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $report = Report::findOrFail($numericId);
        $user = Auth::user();

        if ($user->role !== 'admin' && (int) $report->reporter_id !== (int) $user->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses lampiran ini.');
        }

        abort_unless($report->attachment_path && Storage::disk('local')->exists($report->attachment_path), 404);

        return Storage::disk('local')->download($report->attachment_path);
    }

    /**
     * Admin reports management (/admin/reports).
     */
    public function adminIndex(Request $request)
    {
        $query = Report::with(['reporter', 'statusHistories.admin'])->latest();
        $statusFilter = $request->query('status', 'all');
        $priorityFilter = $request->query('priority', 'all');

        if ($statusFilter !== 'all') {
            $dbStatus = match (strtolower($statusFilter)) {
                'submitted' => 'pending',
                'under_review' => 'under_review',
                'in_progress' => 'in_progress',
                'rejected' => 'rejected',
                'rejected' => 'rejected',
                'resolved' => 'resolved',
                default => $statusFilter,
            };
            $query->where('status', $dbStatus);
        }

        if ($priorityFilter !== 'all') {
            $query->where('priority', strtolower($priorityFilter));
        }

        $allReports = Report::query();
        $urgentCount = (clone $allReports)->where('priority', 'urgent')->count();
        $highCount = (clone $allReports)->where('priority', 'high')->count();
        $inProgressCount = (clone $allReports)->where('status', 'in_progress')->count();
        $submittedCount = $allReports->where('status', 'pending')->count();

        $reports = $query->get()->map(fn ($r) => $this->formatReport($r, true))->toArray();

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
        $data = $request->validate([
            'status' => ['required', Rule::in(['SUBMITTED', 'UNDER_REVIEW', 'IN_PROGRESS', 'RESOLVED', 'REJECTED'])],
            'priority' => ['required', Rule::in(['LOW', 'MEDIUM', 'HIGH', 'URGENT'])],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $dbStatus = match ($data['status']) {
            'SUBMITTED' => 'pending',
            'UNDER_REVIEW' => 'under_review',
            'IN_PROGRESS' => 'in_progress',
            'RESOLVED' => 'resolved',
            'REJECTED' => 'rejected',
        };
        $priority = strtolower($data['priority']);

        DB::transaction(function () use ($numericId, $dbStatus, $priority, $data): void {
            $report = Report::query()->whereKey($numericId)->lockForUpdate()->firstOrFail();
            $previousStatus = $report->status;
            $previousPriority = $report->priority;
            $adminNote = $data['admin_note'] ?? null;

            $report->update([
                'status' => $dbStatus,
                'priority' => $priority,
                'admin_notes' => $adminNote,
            ]);

            if ($previousStatus !== $dbStatus || $previousPriority !== $priority || $adminNote !== null) {
                ReportStatusHistory::create([
                    'report_id' => $report->id,
                    'admin_id' => Auth::id(),
                    'from_status' => $previousStatus,
                    'to_status' => $dbStatus,
                    'from_priority' => $previousPriority,
                    'to_priority' => $priority,
                    'admin_note' => $adminNote,
                ]);
            }

            if ($report->reporter_id) {
                $statusLabel = match ($dbStatus) {
                    'under_review' => 'Sedang Ditelaah',
                    'in_progress' => 'Sedang Ditindaklanjuti',
                    'resolved' => 'Telah Selesai Ditangani',
                    'rejected' => 'Diarsipkan',
                    default => 'Diterima',
                };

                NotificationController::createNotification(
                    $report->reporter_id,
                    'Update Laporan Kampus Aman',
                    'Laporan RPT-'.str_pad((string) $report->id, 4, '0', STR_PAD_LEFT).' ('.$report->category.') kini berstatus: '.$statusLabel.'.',
                    'report_status',
                    '/reports/'.$report->id
                );
            }
        }, attempts: 3);

        return redirect()->back()->with('success', 'Status & tindak lanjut laporan berhasil diperbarui.');
    }
}
