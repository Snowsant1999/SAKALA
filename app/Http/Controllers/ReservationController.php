<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Course;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Format a Reservation model for view compatibility.
     */
    private function formatReservation(Reservation $r): array
    {
        return [
            'id' => 'RSV-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
            'raw_id' => $r->id,
            'room_id' => $r->room_id,
            'room_name' => ($r->room?->name ?? 'Ruangan') . ' (' . ($r->room?->code ?? '') . ')',
            'building' => $r->room?->building?->name ?? 'Gedung TI',
            'floor' => 'Lantai 1',
            'date' => Carbon::parse($r->date)->format('Y-m-d'),
            'time_formatted' => substr($r->start_time, 0, 5) . ' - ' . substr($r->end_time, 0, 5),
            'start_time' => substr($r->start_time, 0, 5),
            'end_time' => substr($r->end_time, 0, 5),
            'requester_name' => $r->user?->name ?? 'Pemohon',
            'requester_email' => $r->user?->email ?? '',
            'requester_role' => ucfirst($r->user?->role ?? 'Mahasiswa'),
            'requester_nim' => $r->user?->nim_nip ?? '—',
            'study_program' => $r->user?->studyProgram?->name ?? 'Teknik Informatika (S1)',
            'class' => 'TIM 5A',
            'course' => 'Kegiatan Akademik & Diskusi',
            'purpose' => $r->purpose,
            'status' => strtoupper($r->status),
            'admin_note' => $r->admin_notes,
            'created_at' => Carbon::parse($r->created_at)->translatedFormat('d M Y, H:i'),
        ];
    }

    /**
     * User's reservations page (/reservations).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $query = Reservation::with(['user.studyProgram', 'room.building'])->latest();

        // Filter by user unless admin
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Filter by status tab
        $statusFilter = $request->query('status', 'all');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $reservations = $query->get()->map(fn($r) => $this->formatReservation($r))->toArray();

        return view('reservations.index', compact('reservations', 'statusFilter'));
    }

    /**
     * Create reservation form (/reservations/create).
     */
    public function create(Request $request)
    {
        $roomId = $request->query('room_id');
        $room = $roomId ? Room::with('building')->find($roomId) : Room::with('building')->first();

        $date = $request->query('date', Carbon::tomorrow()->format('Y-m-d'));
        $startTime = $request->query('start_time', '13:00');
        $endTime = $request->query('end_time', '15:00');

        $courses = Course::all()->map(function ($c) {
            return [
                'id' => $c->id,
                'code' => $c->code,
                'name' => $c->name,
            ];
        })->toArray();

        return view('reservations.create', compact('room', 'roomId', 'date', 'startTime', 'endTime', 'courses'));
    }

    /**
     * Store new reservation request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'purpose' => 'required|string',
        ]);

        $user = Auth::user();
        $roomId = $request->input('room_id');
        if (!$roomId || !Room::find($roomId)) {
            $firstRoom = Room::first();
            $roomId = $firstRoom?->id ?? 1;
        }

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'room_id' => $roomId,
            'date' => $request->input('date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'purpose' => $request->input('purpose'),
            'status' => 'pending',
        ]);

        // Notify admins about the new reservation request
        $admins = User::where('role', 'admin')->get();
        $room = Room::find($roomId);
        foreach ($admins as $admin) {
            NotificationController::createNotification(
                $admin->id,
                'Pengajuan Reservasi Baru',
                "{$user->name} mengajukan peminjaman ruangan " . ($room?->name ?? 'Ruangan') . " pada " . Carbon::parse($reservation->date)->format('d/m/Y') . ".",
                'reservation_new',
                '/admin/reservations'
            );
        }

        return redirect('/reservations')->with('success', 'Request reservasi berhasil diajukan dan sedang menunggu persetujuan admin.');
    }

    /**
     * Show reservation detail (/reservations/{id}).
     */
    public function show($id)
    {
        // Accept either numeric id or RSV-0001 format
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $reservationModel = Reservation::with(['user.studyProgram', 'room.building'])->findOrFail($numericId);
        $reservation = $this->formatReservation($reservationModel);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Admin reservations management (/admin/reservations).
     */
    public function adminIndex(Request $request)
    {
        $activeTab = $request->query('tab', 'pending');
        $allModels = Reservation::with(['user.studyProgram', 'room.building'])->latest()->get();

        $all = $allModels->map(fn($r) => $this->formatReservation($r))->toArray();

        if ($activeTab === 'conflicts') {
            $reservations = [];
        } elseif ($activeTab === 'all') {
            $reservations = $all;
        } else {
            $reservations = array_values(array_filter($all, fn($r) => strtolower($r['status']) === strtolower($activeTab)));
        }

        // Detect conflicts among pending & approved reservations
        $conflicts = [];
        $activeRes = $allModels->whereIn('status', ['pending', 'approved']);
        foreach ($activeRes as $r1) {
            foreach ($activeRes as $r2) {
                if ($r1->id < $r2->id && $r1->room_id === $r2->room_id && $r1->date->format('Y-m-d') === $r2->date->format('Y-m-d')) {
                    if ($r1->start_time < $r2->end_time && $r1->end_time > $r2->start_time) {
                        $conflicts[] = [
                            'room_name' => $r1->room?->name ?? 'Ruangan',
                            'date' => $r1->date->format('Y-m-d'),
                            'requests' => [
                                $this->formatReservation($r1),
                                $this->formatReservation($r2),
                            ],
                        ];
                    }
                }
            }
        }

        $pendingCount = $allModels->where('status', 'pending')->count();
        $approvedCount = $allModels->where('status', 'approved')->count();
        $rejectedCount = $allModels->where('status', 'rejected')->count();
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
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $reservation = Reservation::findOrFail($numericId);

        $adminNote = $request->input('admin_note', 'Disetujui oleh Admin Akademik SAKALA.');
        $reservation->update([
            'status' => 'approved',
            'admin_notes' => $adminNote,
        ]);

        // Trigger notification for the applicant
        NotificationController::createNotification(
            $reservation->user_id,
            'Reservasi Disetujui',
            "Pengajuan peminjaman " . ($reservation->room?->name ?? 'ruangan') . " pada " . $reservation->date->format('d/m/Y') . " telah disetujui admin.",
            'reservation_approved',
            '/reservations/' . $reservation->id
        );

        // Automatically reject conflicting pending requests for the same room and date
        $conflicting = Reservation::where('id', '!=', $reservation->id)
            ->where('room_id', $reservation->room_id)
            ->where('date', $reservation->date)
            ->where('status', 'pending')
            ->where('start_time', '<', $reservation->end_time)
            ->where('end_time', '>', $reservation->start_time)
            ->get();

        foreach ($conflicting as $c) {
            $c->update([
                'status' => 'rejected',
                'admin_notes' => 'Otomatis ditolak: Bentrok dengan reservasi ' . ('RSV-' . str_pad($reservation->id, 4, '0', STR_PAD_LEFT)) . ' yang telah disetujui.',
            ]);

            NotificationController::createNotification(
                $c->user_id,
                'Reservasi Ditolak (Bentrok Jadwal)',
                "Pengajuan peminjaman " . ($c->room?->name ?? 'ruangan') . " pada " . $c->date->format('d/m/Y') . " otomatis ditolak karena bentrok dengan jadwal reservasi lain yang disetujui.",
                'reservation_rejected',
                '/reservations/' . $c->id
            );
        }

        return redirect()->back()->with('success', 'Reservasi berhasil DISETUJUI. Request lain yang bentrok telah otomatis ditolak oleh sistem.');
    }

    /**
     * Admin reject reservation (/admin/reservations/{id}/reject).
     */
    public function adminReject(Request $request, $id)
    {
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $reservation = Reservation::findOrFail($numericId);

        $adminNote = $request->input('admin_note', 'Ditolak: Ruangan tidak dapat digunakan pada waktu yang diminta.');
        $reservation->update([
            'status' => 'rejected',
            'admin_notes' => $adminNote,
        ]);

        // Trigger notification for the applicant
        NotificationController::createNotification(
            $reservation->user_id,
            'Reservasi Ditolak',
            "Pengajuan peminjaman " . ($reservation->room?->name ?? 'ruangan') . " pada " . $reservation->date->format('d/m/Y') . " ditolak oleh admin. Catatan: {$adminNote}",
            'reservation_rejected',
            '/reservations/' . $reservation->id
        );

        return redirect()->back()->with('success', 'Reservasi berhasil DITOLAK.');
    }
}
