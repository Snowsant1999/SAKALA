<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AspirationController extends Controller
{
    /**
     * Format Aspiration model for blade view.
     */
    private function formatAspiration(Aspiration $a): array
    {
        $viewStatus = match ($a->status) {
            'pending' => 'PENDING',
            'processing' => 'DIPROSES',
            'resolved' => 'SELESAI',
            'dismissed' => 'DITOLAK',
            default => 'PENDING',
        };

        $timeline = [
            [
                'time' => Carbon::parse($a->created_at)->translatedFormat('d M Y, H:i'),
                'title' => 'Aspirasi Masuk',
                'description' => 'Aspirasi/keluhan fasilitas telah tercatat di sistem SAKALA.',
                'status' => 'completed',
            ],
        ];

        if ($a->status === 'processing' || $a->status === 'resolved') {
            $timeline[] = [
                'time' => Carbon::parse($a->updated_at)->translatedFormat('d M Y, H:i'),
                'title' => 'Sedang Ditindaklanjuti Bagian Terkait',
                'description' => $a->admin_notes ?: 'Tim sarana dan prasarana sedang menindaklanjuti perbaikan/masukan ini.',
                'status' => 'completed',
            ];
        }

        if ($a->status === 'resolved') {
            $timeline[] = [
                'time' => Carbon::parse($a->updated_at)->translatedFormat('d M Y, H:i'),
                'title' => 'Telah Diselesaikan',
                'description' => 'Perbaikan/keluhan telah ditangani dan dinyatakan selesai oleh teknisi sarpras.',
                'status' => 'completed',
            ];
        }

        return [
            'id' => 'ASP-' . str_pad($a->id, 4, '0', STR_PAD_LEFT),
            'raw_id' => $a->id,
            'category' => $a->category,
            'location' => $a->location,
            'description' => $a->description,
            'status' => $viewStatus,
            'admin_notes' => $a->admin_notes,
            'reporter_name' => $a->reporter?->name ?? 'Pengguna SAKALA',
            'reporter_email' => $a->reporter?->email ?? '',
            'reporter_nim' => $a->reporter?->nim_nip ?? '—',
            'created_at' => Carbon::parse($a->created_at)->translatedFormat('d F Y, H:i'),
            'timeline' => $timeline,
        ];
    }

    /**
     * User Aspirations list (/aspirations)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $query = Aspiration::with('reporter')->latest();

        if ($user->role !== 'admin') {
            $query->where('reporter_id', $user->id);
        }

        $categoryFilter = $request->query('category', 'all');
        if ($categoryFilter !== 'all') {
            $query->where('category', 'like', "%{$categoryFilter}%");
        }

        $statusFilter = $request->query('status', 'all');
        if ($statusFilter !== 'all') {
            $dbStatus = match (strtolower($statusFilter)) {
                'pending' => 'pending',
                'diproses', 'processing' => 'processing',
                'selesai', 'resolved' => 'resolved',
                'ditolak', 'dismissed' => 'dismissed',
                default => $statusFilter,
            };
            $query->where('status', $dbStatus);
        }

        $aspirations = $query->get()->map(fn($a) => $this->formatAspiration($a))->toArray();

        return view('aspirations.index', compact('aspirations', 'categoryFilter', 'statusFilter'));
    }

    /**
     * Create aspiration form (/aspirations/create)
     */
    public function create()
    {
        return view('aspirations.create');
    }

    /**
     * Store new aspiration (/aspirations)
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'location' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $aspiration = Aspiration::create([
            'reporter_id' => $user->id,
            'category' => $request->input('category'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'status' => 'pending',
        ]);

        // Notify admins about the new aspiration
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            NotificationController::createNotification(
                $admin->id,
                'Pengajuan Aspirasi Baru',
                "Aspirasi baru '{$aspiration->category}' untuk lokasi {$aspiration->location} telah diajukan oleh {$user->name}.",
                'aspiration_new',
                '/admin/aspirations'
            );
        }

        return redirect('/aspirations/' . $aspiration->id)->with('success', 'Aspirasi / keluhan fasilitas Anda berhasil dikirimkan ke bagian Sarana & Prasarana.');
    }

    /**
     * Show aspiration detail (/aspirations/{id})
     */
    public function show($id)
    {
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $aspirationModel = Aspiration::with('reporter')->findOrFail($numericId);
        $user = Auth::user();

        if ($user->role !== 'admin' && $aspirationModel->reporter_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat aspirasi ini.');
        }

        $aspiration = $this->formatAspiration($aspirationModel);

        return view('aspirations.show', compact('aspiration'));
    }

    /**
     * Admin Aspirations management (/admin/aspirations)
     */
    public function adminIndex(Request $request)
    {
        $query = Aspiration::with('reporter')->latest();
        $statusFilter = $request->query('status', 'all');
        $categoryFilter = $request->query('category', 'all');

        if ($statusFilter !== 'all') {
            $dbStatus = match (strtolower($statusFilter)) {
                'pending' => 'pending',
                'processing', 'diproses' => 'processing',
                'resolved', 'selesai' => 'resolved',
                'dismissed', 'ditolak' => 'dismissed',
                default => $statusFilter,
            };
            $query->where('status', $dbStatus);
        }

        if ($categoryFilter !== 'all') {
            $query->where('category', 'like', "%{$categoryFilter}%");
        }

        $allAspirations = Aspiration::all();
        $pendingCount = $allAspirations->where('status', 'pending')->count();
        $processingCount = $allAspirations->where('status', 'processing')->count();
        $resolvedCount = $allAspirations->where('status', 'resolved')->count();
        $totalCount = $allAspirations->count();

        $aspirations = $query->get()->map(fn($a) => $this->formatAspiration($a))->toArray();

        return view('admin.aspirations.index', compact(
            'aspirations',
            'statusFilter',
            'categoryFilter',
            'pendingCount',
            'processingCount',
            'resolvedCount',
            'totalCount'
        ));
    }

    /**
     * Admin update status & notes (/admin/aspirations/{id}/update)
     */
    public function adminUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $aspiration = Aspiration::findOrFail($numericId);

        $dbStatus = match (strtoupper($request->input('status'))) {
            'PENDING' => 'pending',
            'DIPROSES', 'PROCESSING' => 'processing',
            'SELESAI', 'RESOLVED' => 'resolved',
            'DITOLAK', 'DISMISSED' => 'dismissed',
            default => 'pending',
        };

        $aspiration->update([
            'status' => $dbStatus,
            'admin_notes' => $request->input('admin_notes', $aspiration->admin_notes),
        ]);

        if ($aspiration->reporter_id) {
            $statusLabel = match($dbStatus) {
                'processing' => 'Sedang Ditindaklanjuti',
                'resolved' => 'Telah Selesai Ditangani',
                'dismissed' => 'Ditolak / Diarsipkan',
                default => 'Diterima',
            };

            NotificationController::createNotification(
                $aspiration->reporter_id,
                'Update Layanan Aspirasi',
                "Aspirasi ASP-" . str_pad($aspiration->id, 4, '0', STR_PAD_LEFT) . " ({$aspiration->category}) kini berstatus: {$statusLabel}.",
                'aspiration_status',
                '/aspirations/' . $aspiration->id
            );
        }

        return redirect()->back()->with('success', 'Status aspirasi dan catatan tindak lanjut berhasil diperbarui.');
    }
}
