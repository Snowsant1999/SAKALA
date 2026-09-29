@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Layanan Aspirasi & Fasilitas')
@section('page-title', 'Layanan Aspirasi & Keluhan Sarpras')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header Actions Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            @foreach([
                'all' => 'Semua Kategori',
                'Kerusakan Fasilitas' => 'Kerusakan Fasilitas',
                'Kebersihan' => 'Kebersihan',
                'Koneksi Internet' => 'Koneksi Internet',
                'Sarana Kelas' => 'Sarana Kelas',
            ] as $key => $label)
                <a href="{{ url('/aspirations?category=' . urlencode($key)) }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all shrink-0 {{ ($categoryFilter === $key) ? 'bg-navy-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <a href="{{ url('/aspirations/create') }}" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Ajukan Aspirasi / Keluhan
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Aspirations List Cards --}}
    <div class="space-y-4">
        @forelse($aspirations as $asp)
            <div class="card p-6 hover:shadow-md transition-all border-l-4 {{ $asp['status'] === 'SELESAI' ? 'border-l-emerald-500' : ($asp['status'] === 'DIPROSES' ? 'border-l-sky-500' : ($asp['status'] === 'DITOLAK' ? 'border-l-red-500' : 'border-l-amber-500')) }}">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-400">{{ $asp['id'] }}</span>
                        <span class="badge badge-primary text-xs font-semibold">{{ $asp['category'] }}</span>
                        
                        @if($asp['status'] === 'SELESAI')
                            <span class="badge badge-success text-[11px]">✓ Selesai Ditangani</span>
                        @elseif($asp['status'] === 'DIPROSES')
                            <span class="badge badge-info text-[11px]">⚙ Sedang Ditindaklanjuti</span>
                        @elseif($asp['status'] === 'DITOLAK')
                            <span class="badge badge-danger text-[11px]">✕ Ditolak</span>
                        @else
                            <span class="badge badge-warning text-[11px]">⏳ Menunggu Respon Sarpras</span>
                        @endif
                    </div>

                    <span class="text-xs text-slate-400">Diajukan: {{ $asp['created_at'] }}</span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="text-xs text-slate-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        <span>Lokasi/Ruangan: <strong>{{ $asp['location'] }}</strong></span>
                    </div>

                    <p class="text-sm text-slate-700 line-clamp-2 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-100">
                        {{ $asp['description'] }}
                    </p>

                    @if(!empty($asp['admin_notes']))
                        <div class="text-xs text-navy-800 bg-navy-50/60 p-3 rounded-lg border border-navy-100">
                            <strong>Respon Bagian Sarpras:</strong> {{ $asp['admin_notes'] }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <span class="text-[11px] text-slate-400">Pelapor: {{ $asp['reporter_name'] }}</span>
                    <a href="{{ url('/aspirations/' . $asp['raw_id']) }}" class="btn btn-secondary text-xs">
                        Lihat Progres Penanganan &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.83M11.42 15.17l-3.96 3.96a2.653 2.653 0 01-3.75-3.75l3.96-3.96m5.75 3.75l-5.75-5.75M10.5 4.5l3 3m-3-3l-3 3m3-3v8.25" /></svg>
                <div class="text-base font-semibold text-slate-700 mb-1">Belum ada aspirasi / keluhan yang diajukan</div>
                <p class="text-sm text-slate-500 mb-4">Temukan kendala fasilitas ruangan, AC rusak, atau sarana belajar? Sampaikan aspirasi Anda di sini.</p>
                <a href="{{ url('/aspirations/create') }}" class="btn btn-primary text-xs">Ajukan Sekarang</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
