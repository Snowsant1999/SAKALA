@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Reservasi Saya')
@section('page-title', 'Reservasi Saya')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header Actions & Filter Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            @foreach([
                'all' => 'Semua',
                'pending' => 'Menunggu (Pending)',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
            ] as $key => $label)
                <a href="{{ url('/reservations?status=' . $key) }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all shrink-0 {{ ($statusFilter === $key) ? 'bg-navy-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <a href="{{ url('/rooms') }}" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Eksplorasi Ruangan & Ajukan Baru
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Reservations List Table / Cards --}}
    <div class="space-y-4">
        @forelse($reservations as $r)
            <div class="card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-md transition-all border-l-4 {{ $r['status'] === 'APPROVED' ? 'border-l-emerald-500' : ($r['status'] === 'PENDING' ? 'border-l-amber-500' : 'border-l-red-500') }}">
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs text-slate-400 font-semibold">{{ $r['id'] }}</span>
                        @if($r['status'] === 'APPROVED')
                            <span class="badge badge-success">✓ Disetujui (Approved)</span>
                        @elseif($r['status'] === 'PENDING')
                            <span class="badge badge-warning">⏳ Menunggu Persetujuan Admin</span>
                        @else
                            <span class="badge badge-danger">✗ Ditolak (Rejected)</span>
                        @endif
                        <span class="text-xs text-slate-400">• Diajukan: {{ $r['created_at'] }}</span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-800">{{ $r['room_name'] }}</h3>
                    <div class="text-xs text-slate-500 flex flex-wrap items-center gap-4">
                        <span>🏢 {{ $r['building'] }} ({{ $r['floor'] }})</span>
                        <span>📅 {{ \Carbon\Carbon::parse($r['date'])->translatedFormat('d F Y') }}</span>
                        <span>⏰ {{ $r['time_formatted'] }} WITA</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-xs text-slate-600 mt-2">
                        <div><strong>Tujuan:</strong> {{ $r['purpose'] }}</div>
                        <div><strong>Mata Kuliah / Kelas:</strong> {{ $r['course'] }} ({{ $r['class'] }})</div>
                        @if(!empty($r['admin_note']))
                            <div class="mt-1 pt-1 border-t border-slate-200 text-navy-800 font-medium">
                                <strong>Catatan Admin:</strong> {{ $r['admin_note'] }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="shrink-0 self-end md:self-center">
                    <a href="{{ url('/reservations/' . $r['id']) }}" class="btn btn-secondary text-xs">
                        Lihat Detail & Status
                    </a>
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                <div class="text-base font-semibold text-slate-700 mb-1">Belum ada pengajuan reservasi</div>
                <p class="text-sm text-slate-500 mb-4">Anda dapat mengajukan peminjaman ruangan dengan memilih slot kosong di menu Ruangan Belajar.</p>
                <a href="{{ url('/rooms') }}" class="btn btn-primary text-xs">Pilih Ruangan</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
