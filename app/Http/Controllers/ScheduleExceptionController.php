<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\ScheduleException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ScheduleExceptionController extends Controller
{
    public function store(Request $request, int $scheduleId): RedirectResponse
    {
        $schedule = Schedule::with('courseClass')->findOrFail($scheduleId);
        $user = $request->user();
        $this->authorizeScheduleChange($user, $schedule);

        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $date = Carbon::createFromFormat('!Y-m-d', $data['date']);

        if ($date->locale('id')->isoFormat('dddd') !== $schedule->day) {
            throw ValidationException::withMessages([
                'date' => 'Tanggal harus sesuai dengan hari jadwal kelas ini.',
            ]);
        }

        if ($schedule->mode !== 'ONSITE') {
            throw ValidationException::withMessages([
                'date' => 'Hanya sesi tatap muka yang perlu dibatalkan untuk penggunaan ruangan.',
            ]);
        }

        $schedule->exceptions()->firstOrCreate(
            ['date' => $date->toDateString()],
            [
                'cancelled_by' => $user->id,
                'reason' => $data['reason'] ?? null,
            ],
        );

        return back()->with('success', 'Sesi kelas pada tanggal tersebut dibatalkan; jadwal rutin tetap berlaku.');
    }

    public function destroy(Request $request, int $scheduleId, int $exceptionId): RedirectResponse
    {
        $schedule = Schedule::with('courseClass')->findOrFail($scheduleId);
        $this->authorizeScheduleChange($request->user(), $schedule);

        $exception = ScheduleException::query()
            ->where('schedule_id', $schedule->id)
            ->whereKey($exceptionId)
            ->firstOrFail();

        $approvedReservationExists = Reservation::query()
            ->where('room_id', $schedule->room_id)
            ->whereDate('date', $exception->date->toDateString())
            ->where('status', 'approved')
            ->where('start_time', '<', $schedule->end_time)
            ->where('end_time', '>', $schedule->start_time)
            ->exists();

        if ($approvedReservationExists) {
            return back()->with('error', 'Jadwal tidak dapat dipulihkan selama masih ada reservasi yang disetujui pada slot ini.');
        }

        $exception->delete();

        return back()->with('success', 'Sesi kelas dipulihkan; slot kembali mengikuti jadwal rutin.');
    }

    private function authorizeScheduleChange(User $user, Schedule $schedule): void
    {
        if ($user->role === 'admin') {
            return;
        }

        abort_unless(
            $user->role === 'dosen' && (int) $schedule->courseClass?->lecturer_id === (int) $user->id,
            403,
        );
    }
}
