<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Services\ReservationConflictDetector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * Format a Reservation model for view compatibility.
     */
    private function formatReservation(Reservation $r): array
    {
        return [
            'id' => 'RSV-'.str_pad($r->id, 4, '0', STR_PAD_LEFT),
            'raw_id' => $r->id,
            'room_id' => $r->room_id,
            'room_name' => ($r->room?->name ?? 'Ruangan').' ('.($r->room?->code ?? '').')',
            'building' => $r->room?->building?->name ?? 'Gedung TI',
            'floor' => $r->room?->floorRecord?->label ?? ('Lantai '.$r->room?->floor),
            'date' => Carbon::parse($r->date)->format('Y-m-d'),
            'time_formatted' => substr($r->start_time, 0, 5).' - '.substr($r->end_time, 0, 5),
            'start_time' => substr($r->start_time, 0, 5),
            'end_time' => substr($r->end_time, 0, 5),
            'requester_name' => $r->user?->name ?? 'Pemohon',
            'requester_email' => $r->user?->email ?? '',
            'requester_role' => ucfirst($r->user?->role ?? 'Mahasiswa'),
            'requester_nim' => $r->user?->nim_nip ?? '—',
            'study_program' => $r->user?->studyProgram?->name ?? $r->course?->studyProgram?->name ?? 'Belum ditentukan',
            'class' => $r->courseClass?->name ?? 'Belum ditetapkan',
            'course' => $r->course?->name ?? $r->courseClass?->course?->name ?? 'Kegiatan umum',
            'purpose' => $r->purpose,
            'notes' => $r->notes,
            'status' => match (strtolower($r->status)) {
                'approved' => $r->date->isBefore(today()) ? 'COMPLETED' : 'APPROVED',
                'cancelled' => 'CANCELLED',
                'completed' => 'COMPLETED',
                default => strtoupper($r->status),
            },
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
        if (! $user) {
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

        $reservations = $query->get()->map(fn ($r) => $this->formatReservation($r))->toArray();

        return view('reservations.index', compact('reservations', 'statusFilter'));
    }

    /**
     * Create reservation form (/reservations/create).
     */
    public function create(Request $request)
    {
        $roomId = $request->integer('room_id') ?: null;
        $room = $roomId ? Room::with(['building', 'floorRecord'])->find($roomId) : null;
        $rooms = Room::with(['building', 'floorRecord'])->orderBy('name')->get();

        $requestedDate = $request->query('date');
        $date = Carbon::tomorrow()->format('Y-m-d');
        if (is_string($requestedDate) && Validator::make(
            ['date' => $requestedDate],
            ['date' => ['date_format:Y-m-d', 'after_or_equal:today']],
        )->passes()) {
            $date = $requestedDate;
        }
        $startTime = $request->query('start_time', '13:00');
        $endTime = $request->query('end_time', '15:00');

        $user = Auth::user();
        $classQuery = CourseClass::with('course')->orderBy('name');

        if ($user->role === 'mahasiswa') {
            if ($user->cohort_id === null) {
                $classQuery->whereRaw('1 = 0');
            } else {
                $classQuery->where('cohort_id', $user->cohort_id);
            }
        } elseif ($user->role === 'dosen') {
            $classQuery->where('lecturer_id', $user->id);
        }

        $courseClasses = $classQuery->get();
        $courses = $courseClasses->pluck('course')->filter()->unique('id')->map(function ($course) {
            return [
                'id' => $course->id,
                'code' => $course->code,
                'name' => $course->name,
            ];
        })->toArray();

        $classes = $courseClasses->map(fn (CourseClass $class): array => [
            'id' => $class->id,
            'name' => $class->name,
            'course_id' => $class->course_id,
            'course' => $class->course?->name ?? 'Mata Kuliah',
        ])->all();

        return view('reservations.create', compact('room', 'roomId', 'rooms', 'date', 'startTime', 'endTime', 'courses', 'classes'));
    }

    /**
     * Store new reservation request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'course_class_id' => ['nullable', 'integer', 'exists:course_classes,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose' => ['required', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $courseClass = isset($data['course_class_id']) ? CourseClass::findOrFail($data['course_class_id']) : null;

        if ($user->role === 'mahasiswa') {
            $belongsToCohort = $courseClass !== null && $courseClass->cohort_id === $user->cohort_id;
            if (! $belongsToCohort) {
                throw ValidationException::withMessages([
                    'course_class_id' => 'Pilih kelas mata kuliah dari rombongan Anda.',
                ]);
            }
        }

        if ($user->role === 'dosen' && $courseClass && (int) $courseClass->lecturer_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'course_class_id' => 'Anda hanya dapat mengajukan reservasi untuk kelas yang Anda ampu.',
            ]);
        }
        if ($courseClass && isset($data['course_id']) && $courseClass->course_id !== (int) $data['course_id']) {
            throw ValidationException::withMessages(['course_class_id' => 'Kelas tidak terdaftar pada mata kuliah yang dipilih.']);
        }
        if ($courseClass && ! isset($data['course_id'])) {
            $data['course_id'] = $courseClass->course_id;
        }

        $approvedConflict = Reservation::where('room_id', $data['room_id'])
            ->where('date', $data['date'])
            ->where('status', 'approved')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();
        if ($approvedConflict) {
            throw ValidationException::withMessages(['room_id' => 'Ruangan sudah memiliki reservasi yang disetujui pada rentang waktu tersebut.']);
        }

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'room_id' => $data['room_id'],
            'course_id' => $data['course_id'] ?? null,
            'course_class_id' => $data['course_class_id'] ?? null,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'purpose' => $data['purpose'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Notify admins about the new reservation request
        $admins = User::where('role', 'admin')->get();
        $room = Room::find($data['room_id']);
        foreach ($admins as $admin) {
            NotificationController::createNotification(
                $admin->id,
                'Pengajuan Reservasi Baru',
                "{$user->name} mengajukan peminjaman ruangan ".($room?->name ?? 'Ruangan').' pada '.Carbon::parse($reservation->date)->format('d/m/Y').'.',
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
        $reservationModel = Reservation::with(['user.studyProgram', 'room.building', 'room.floorRecord', 'course.studyProgram', 'courseClass.course.studyProgram'])->findOrFail($numericId);
        $user = Auth::user();
        if ($user->role !== 'admin' && (int) $reservationModel->user_id !== (int) $user->id) {
            abort(403, 'Anda tidak memiliki izin untuk melihat reservasi ini.');
        }

        $reservation = $this->formatReservation($reservationModel);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Admin reservations management (/admin/reservations).
     */
    public function adminIndex(Request $request, ReservationConflictDetector $conflictDetector)
    {
        $activeTab = $request->query('tab', 'pending');
        $allModels = Reservation::with([
            'user.studyProgram',
            'room.building',
            'room.floorRecord',
            'course.studyProgram',
            'courseClass.course.studyProgram',
        ])->latest()->get();

        $all = $allModels->map(fn ($r) => $this->formatReservation($r))->toArray();

        if ($activeTab === 'conflicts') {
            $reservations = [];
        } elseif ($activeTab === 'all') {
            $reservations = $all;
        } elseif ($activeTab === 'today') {
            $reservations = array_values(array_filter($all, fn (array $reservation): bool => $reservation['date'] === now()->format('Y-m-d')));
        } elseif ($activeTab === 'upcoming') {
            $reservations = array_values(array_filter($all, fn (array $reservation): bool => $reservation['date'] > now()->format('Y-m-d')));
        } elseif ($activeTab === 'completed') {
            $reservations = array_values(array_filter($all, fn (array $reservation): bool => $reservation['status'] === 'COMPLETED'));
        } elseif ($activeTab === 'cancelled') {
            $reservations = array_values(array_filter($all, fn (array $reservation): bool => $reservation['status'] === 'CANCELLED'));
        } else {
            $reservations = array_values(array_filter($all, fn ($r) => strtolower($r['status']) === strtolower($activeTab)));
        }

        $conflicts = array_map(
            fn (array $group): array => $this->formatConflictGroup($group),
            $conflictDetector->groups($allModels),
        );

        $pendingCount = $allModels->where('status', 'pending')->count();
        $approvedCount = $allModels->where('status', 'approved')->count();
        $rejectedCount = $allModels->where('status', 'rejected')->count();
        $completedCount = $allModels->filter(fn (Reservation $reservation): bool => $reservation->status === 'completed' || ($reservation->status === 'approved' && $reservation->date->isBefore(today())))->count();
        $cancelledCount = $allModels->where('status', 'cancelled')->count();
        $todayCount = $allModels->where('date', today())->count();
        $upcomingCount = $allModels->filter(fn (Reservation $reservation): bool => $reservation->date->isAfter(today()))->count();
        $conflictCount = count($conflicts);

        return view('admin.reservations.index', compact(
            'reservations',
            'conflicts',
            'activeTab',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'completedCount',
            'cancelledCount',
            'conflictCount',
            'todayCount',
            'upcomingCount'
        ));
    }

    private function formatConflictGroup(array $reservations): array
    {
        $first = $reservations[0];

        return [
            'room_name' => $first->room?->name ?? 'Ruangan',
            'date' => $first->date->format('Y-m-d'),
            'requests' => array_map(fn (Reservation $reservation): array => $this->formatReservation($reservation), $reservations),
        ];
    }

    /**
     * Admin approve reservation (/admin/reservations/{id}/approve).
     */
    public function adminApprove(Request $request, $id)
    {
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $failure = DB::transaction(function () use ($numericId, $validated): ?string {
            $reservation = Reservation::query()->whereKey($numericId)->lockForUpdate()->firstOrFail();
            if ($reservation->status !== 'pending') {
                return 'Hanya reservasi yang masih menunggu yang dapat disetujui.';
            }

            $approvedOverlap = Reservation::query()
                ->where('room_id', $reservation->room_id)
                ->where('date', $reservation->date)
                ->where('status', 'approved')
                ->where('start_time', '<', $reservation->end_time)
                ->where('end_time', '>', $reservation->start_time)
                ->lockForUpdate()
                ->exists();

            if ($approvedOverlap) {
                return 'Tidak dapat menyetujui reservasi karena slot waktu sudah dikunci oleh reservasi lain.';
            }

            $adminNote = $validated['admin_note'] ?? 'Disetujui oleh Admin Akademik SAKALA.';
            $reservation->update([
                'status' => 'approved',
                'admin_notes' => $adminNote,
                'reviewed_by' => Auth::id(),
            ]);

            $conflicting = Reservation::query()
                ->where('id', '!=', $reservation->id)
                ->where('room_id', $reservation->room_id)
                ->where('date', $reservation->date)
                ->where('status', 'pending')
                ->where('start_time', '<', $reservation->end_time)
                ->where('end_time', '>', $reservation->start_time)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            NotificationController::createNotification(
                $reservation->user_id,
                'Reservasi Disetujui',
                'Pengajuan peminjaman '.($reservation->room?->name ?? 'ruangan').' pada '.$reservation->date->format('d/m/Y').' telah disetujui admin.',
                'reservation_approved',
                '/reservations/'.$reservation->id
            );

            foreach ($conflicting as $conflict) {
                $conflict->update([
                    'status' => 'rejected',
                    'admin_notes' => 'Otomatis ditolak: Slot disetujui untuk RSV-'.str_pad((string) $reservation->id, 4, '0', STR_PAD_LEFT).'.',
                    'reviewed_by' => Auth::id(),
                ]);

                NotificationController::createNotification(
                    $conflict->user_id,
                    'Reservasi Ditolak (Bentrok Jadwal)',
                    'Pengajuan peminjaman '.($conflict->room?->name ?? 'ruangan').' pada '.$conflict->date->format('d/m/Y').' otomatis ditolak karena bentrok dengan reservasi yang disetujui.',
                    'reservation_rejected',
                    '/reservations/'.$conflict->id
                );
            }

            return null;
        }, attempts: 3);

        if ($failure !== null) {
            return redirect()->back()->with('error', $failure);
        }

        return redirect()->back()->with('success', 'Reservasi disetujui; pengajuan pending yang bertabrakan otomatis ditolak.');
    }

    /**
     * Admin reject reservation (/admin/reservations/{id}/reject).
     */
    public function adminReject(Request $request, $id)
    {
        $numericId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $failure = DB::transaction(function () use ($numericId, $validated): ?string {
            $reservation = Reservation::query()->whereKey($numericId)->lockForUpdate()->firstOrFail();
            if ($reservation->status !== 'pending') {
                return 'Hanya reservasi yang masih menunggu yang dapat ditolak.';
            }

            $adminNote = $validated['admin_note'] ?? 'Ditolak: Ruangan tidak dapat digunakan pada waktu yang diminta.';
            $reservation->update([
                'status' => 'rejected',
                'admin_notes' => $adminNote,
                'reviewed_by' => Auth::id(),
            ]);

            NotificationController::createNotification(
                $reservation->user_id,
                'Reservasi Ditolak',
                'Pengajuan peminjaman '.($reservation->room?->name ?? 'ruangan').' pada '.$reservation->date->format('d/m/Y').' ditolak oleh admin. Catatan: '.$adminNote,
                'reservation_rejected',
                '/reservations/'.$reservation->id
            );

            return null;
        }, attempts: 3);

        if ($failure !== null) {
            return redirect()->back()->with('error', $failure);
        }

        return redirect()->back()->with('success', 'Reservasi berhasil DITOLAK.');
    }
}
