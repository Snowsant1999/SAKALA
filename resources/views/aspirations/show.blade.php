@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Detail Aspirasi ' . $aspiration['id'])
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ session('user_role') === 'admin' ? url('/admin/aspirations') : url('/aspirations') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    <span>Detail Aspirasi / Keluhan — {{ $aspiration['id'] }}</span>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in-up">

    {{-- Alert Flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>✓ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Status Banner --}}
    <div class="card p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-l-4 {{ $aspiration['status'] === 'SELESAI' ? 'border-l-emerald-500 bg-emerald-50/20' : ($aspiration['status'] === 'DIPROSES' ? 'border-l-sky-500 bg-sky-50/20' : ($aspiration['status'] === 'DITOLAK' ? 'border-l-red-500 bg-red-50/20' : 'border-l-amber-500 bg-amber-50/20')) }}">
        <div>
            <div class="text-xs text-slate-400 mb-1">Status Penanganan Sarpras:</div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($aspiration['status'] === 'SELESAI')
                    <span class="badge badge-success text-sm py-1">✓ Selesai Ditangani</span>
                @elseif($aspiration['status'] === 'DIPROSES')
                    <span class="badge badge-info text-sm py-1">⚙ Sedang Ditindaklanjuti Teknisi</span>
                @elseif($aspiration['status'] === 'DITOLAK')
                    <span class="badge badge-danger text-sm py-1">✕ Ditolak</span>
                @else
                    <span class="badge badge-warning text-sm py-1">⏳ Menunggu Respon Sarpras</span>
                @endif
                <span class="text-xs text-slate-500">• Kategori: <strong>{{ $aspiration['category'] }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-xs text-slate-500">
                ID: <strong class="font-mono text-slate-700">{{ $aspiration['id'] }}</strong>
            </div>
            @if(session('user_role') === 'admin')
                <button onclick="document.getElementById('admin-update-modal').classList.remove('hidden')" class="btn btn-primary btn-sm text-xs">
                    Update Status Sarpras
                </button>
            @endif
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Details & Description --}}
        <div class="lg:col-span-2 space-y-6">
            @if(session('user_role') === 'admin')
                {{-- Reporter Info Box --}}
                <div class="card p-6 space-y-3 bg-slate-50 border-slate-200">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        Informasi Pelapor
                    </h3>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400">Nama Pelapor:</span>
                            <div class="font-bold text-slate-800">{{ $aspiration['reporter_name'] }}</div>
                        </div>
                        <div>
                            <span class="text-slate-400">NIM / NIP:</span>
                            <div class="font-medium text-slate-700">{{ $aspiration['reporter_nim'] }}</div>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-400">Waktu Diajukan:</span>
                            <div class="font-medium text-slate-700">{{ $aspiration['created_at'] }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                    Detail Keluhan / Fasilitas
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <div class="text-slate-400 mb-0.5">Kategori:</div>
                        <div class="font-semibold text-slate-800">{{ $aspiration['category'] }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 mb-0.5">Lokasi Spesifik:</div>
                        <div class="font-semibold text-slate-800">{{ $aspiration['location'] }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-400 mb-1.5 font-semibold">Deskripsi Kendala:</div>
                    <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                        {{ $aspiration['description'] }}
                    </p>
                </div>
            </div>

            {{-- Admin Response Box --}}
            @if(!empty($aspiration['admin_notes']))
                <div class="card p-6 bg-navy-50/50 border-navy-200 space-y-2">
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-wider">Tanggapan / Catatan Teknisi Sarpras</h3>
                    <p class="text-sm text-navy-950 leading-relaxed">{{ $aspiration['admin_notes'] }}</p>
                </div>
            @endif
        </div>

        {{-- Right: Timeline --}}
        <div class="space-y-6">
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Riwayat Penanganan
                </h3>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($aspiration['timeline'] ?? [] as $step)
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-navy-600 ring-4 ring-white"></span>
                            <div class="text-xs font-bold text-slate-800">{{ $step['title'] }}</div>
                            <div class="text-[11px] text-slate-400 mb-1">{{ $step['time'] }}</div>
                            <div class="text-xs text-slate-600 leading-relaxed">{{ $step['description'] }}</div>
                        </div>
                    @empty
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-navy-600 ring-4 ring-white"></span>
                            <div class="text-xs font-bold text-slate-800">Aspirasi Terkirim</div>
                            <div class="text-[11px] text-slate-400 mb-1">{{ $aspiration['created_at'] }}</div>
                            <div class="text-xs text-slate-600">Aspirasi baru diajukan ke sistem.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-100 text-[11px] text-slate-500 leading-relaxed">
                Bagian Sarana & Prasarana berkomitmen menjaga kenyamanan dan kelayakan fasilitas akademik di seluruh lingkungan kampus.
            </div>
        </div>
    </div>
</div>

@if(session('user_role') === 'admin')
{{-- Admin Update Modal --}}
<div id="admin-update-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-fade-in-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-800">Update Status Aspirasi #{{ $aspiration['id'] }}</h3>
            <button onclick="document.getElementById('admin-update-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ url('/admin/aspirations/' . $aspiration['raw_id'] . '/update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="form-group">
                <label class="form-label text-xs">Status Penanganan</label>
                <select name="status" class="form-select" required>
                    <option value="PENDING" {{ $aspiration['status'] === 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu Respon)</option>
                    <option value="DIPROSES" {{ $aspiration['status'] === 'DIPROSES' ? 'selected' : '' }}>DIPROSES (Sedang Ditindaklanjuti)</option>
                    <option value="SELESAI" {{ $aspiration['status'] === 'SELESAI' ? 'selected' : '' }}>SELESAI (Perbaikan Rampung)</option>
                    <option value="DITOLAK" {{ $aspiration['status'] === 'DITOLAK' ? 'selected' : '' }}>DITOLAK (Tidak Valid / Dibatalkan)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label text-xs">Catatan Tindak Lanjut / Respon Sarpras</label>
                <textarea name="admin_notes" rows="4" class="form-textarea text-xs" placeholder="Tuliskan perkembangan perbaikan fasilitas...">{{ $aspiration['admin_notes'] ?? '' }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('admin-update-modal').classList.add('hidden')" class="btn btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn btn-primary text-xs">Simpan Status</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
