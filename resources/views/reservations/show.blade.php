@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Detail Reservasi ' . $reservation['id'])
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ session('user_role') === 'admin' ? url('/admin/reservations') : url('/reservations') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    <span>Detail Reservasi — {{ $reservation['id'] }}</span>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in-up">
    {{-- Status Banner --}}
    <div class="card p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-l-4 {{ $reservation['status'] === 'APPROVED' ? 'border-l-emerald-500 bg-emerald-50/20' : ($reservation['status'] === 'PENDING' ? 'border-l-amber-500 bg-amber-50/20' : 'border-l-red-500 bg-red-50/20') }}">
        <div>
            <div class="text-xs text-slate-400 mb-1">Status Pengajuan:</div>
            <div class="flex items-center gap-2">
                @if($reservation['status'] === 'APPROVED')
                    <span class="badge badge-success text-sm py-1">✓ Disetujui (APPROVED)</span>
                @elseif($reservation['status'] === 'PENDING')
                    <span class="badge badge-warning text-sm py-1">⏳ Menunggu Review Admin (PENDING)</span>
                @else
                    <span class="badge badge-danger text-sm py-1">✗ Ditolak (REJECTED)</span>
                @endif
                <span class="text-xs text-slate-500">• ID: {{ $reservation['id'] }}</span>
            </div>
        </div>

        <div class="text-xs text-slate-500">
            Waktu Pengajuan: <strong>{{ $reservation['created_at'] }}</strong>
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Room & Schedule Info --}}
        <div class="card p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" /></svg>
                Informasi Ruangan & Jadwal
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Ruangan:</span>
                    <span class="font-bold text-slate-800">{{ $reservation['room_name'] }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Gedung & Lantai:</span>
                    <span class="font-medium text-slate-700">{{ $reservation['building'] }} ({{ $reservation['floor'] }})</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Tanggal:</span>
                    <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($reservation['date'])->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Waktu / Durasi:</span>
                    <span class="font-bold text-slate-800">{{ $reservation['time_formatted'] }} WITA</span>
                </div>
            </div>
        </div>

        {{-- Requester Info --}}
        <div class="card p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                Data Pemohon
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Nama Pemohon:</span>
                    <span class="font-bold text-slate-800">{{ $reservation['requester_name'] }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Peran / NIM:</span>
                    <span class="font-medium text-slate-700">{{ $reservation['requester_role'] }} ({{ $reservation['requester_nim'] ?? '-' }})</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Kelas / Prodi:</span>
                    <span class="font-medium text-slate-700">{{ $reservation['class'] }} • {{ $reservation['study_program'] }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Mata Kuliah / Kegiatan:</span>
                    <span class="font-semibold text-slate-800">{{ $reservation['course'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Purpose & Admin Feedback --}}
    <div class="card p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3">Tujuan & Catatan Penanganan</h3>
        
        <div>
            <div class="text-xs font-semibold text-slate-500 mb-1">Tujuan Penggunaan:</div>
            <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">{{ $reservation['purpose'] }}</p>
        </div>

        @if(!empty($reservation['notes']))
            <div>
                <div class="text-xs font-semibold text-slate-500 mb-1">Catatan Pemohon:</div>
                <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg">{{ $reservation['notes'] }}</p>
            </div>
        @endif

        @if(!empty($reservation['admin_note']))
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="text-xs font-bold text-navy-800 mb-1">Catatan / Keputusan Admin:</div>
                <div class="p-4 rounded-xl {{ $reservation['status'] === 'APPROVED' ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-red-50 text-red-900 border border-red-200' }} text-xs leading-relaxed font-medium">
                    {{ $reservation['admin_note'] }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
