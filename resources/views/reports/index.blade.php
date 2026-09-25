@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Laporan Saya — Kampus Aman')
@section('page-title', 'Kampus Aman — Laporan Saya')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header Actions Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            @foreach([
                'all' => 'Semua Laporan',
                'intimidasi' => 'Intimidasi',
                'perundungan' => 'Perundungan',
                'pelecehan verbal' => 'Pelecehan',
            ] as $key => $label)
                <a href="{{ url('/reports?category=' . $key) }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all shrink-0 {{ ($categoryFilter === $key) ? 'bg-navy-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <a href="{{ url('/reports/create') }}" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Buat Laporan Baru
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Reports List Cards --}}
    <div class="space-y-4">
        @forelse($reports as $rpt)
            <div class="card p-6 hover:shadow-md transition-all border-l-4 {{ $rpt['status'] === 'RESOLVED' ? 'border-l-emerald-500' : ($rpt['status'] === 'IN_PROGRESS' ? 'border-l-sky-500' : ($rpt['status'] === 'UNDER_REVIEW' ? 'border-l-amber-500' : 'border-l-purple-500')) }}">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-400">{{ $rpt['id'] }}</span>
                        <span class="badge badge-primary text-xs font-semibold">{{ $rpt['category'] }}</span>
                        
                        @if($rpt['status'] === 'RESOLVED')
                            <span class="badge badge-success text-[11px]">✓ Selesai Ditangani (RESOLVED)</span>
                        @elseif($rpt['status'] === 'IN_PROGRESS')
                            <span class="badge badge-info text-[11px]">⚙ Sedang Ditangani (IN PROGRESS)</span>
                        @elseif($rpt['status'] === 'UNDER_REVIEW')
                            <span class="badge badge-warning text-[11px]">🔍 Dalam Peninjauan Satgas (UNDER REVIEW)</span>
                        @else
                            <span class="badge bg-purple-50 text-purple-700 text-[11px] font-semibold">📩 Laporan Terkirim (SUBMITTED)</span>
                        @endif
                    </div>

                    <span class="text-xs text-slate-400">Tanggal Kejadian: {{ \Carbon\Carbon::parse($rpt['incident_date'])->translatedFormat('d F Y') }}</span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="text-xs text-slate-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        <span>Lokasi: <strong>{{ $rpt['location'] }}</strong></span>
                    </div>

                    <p class="text-sm text-slate-700 line-clamp-2 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-100">
                        {{ $rpt['description'] }}
                    </p>

                    @if(!empty($rpt['admin_note']))
                        <div class="text-xs text-navy-800 bg-navy-50/60 p-3 rounded-lg border border-navy-100">
                            <strong>Respon Satgas:</strong> {{ $rpt['admin_note'] }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <span class="text-[11px] text-slate-400">Lampiran: {{ $rpt['attachments'] ?? 'Tidak ada lampiran' }}</span>
                    <a href="{{ url('/reports/' . $rpt['id']) }}" class="btn btn-secondary text-xs">
                        Lihat Progres Penanganan & Timeline &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" /></svg>
                <div class="text-base font-semibold text-slate-700 mb-1">Belum ada laporan yang Anda buat</div>
                <p class="text-sm text-slate-500 mb-4">Jika Anda mengalami atau menyaksikan kejadian tidak menyenangkan di lingkungan kampus, Anda dapat melaporkannya secara rahasia di sini.</p>
                <a href="{{ url('/reports/create') }}" class="btn btn-primary text-xs">Buat Laporan</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
