@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Detail Laporan ' . $report['id'])
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ session('user_role') === 'admin' ? url('/admin/reports') : url('/reports') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    <span>Detail Laporan — {{ $report['id'] }}</span>
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
    <div class="card p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-l-4 {{ $report['status'] === 'RESOLVED' ? 'border-l-emerald-500 bg-emerald-50/20' : ($report['status'] === 'IN_PROGRESS' ? 'border-l-sky-500 bg-sky-50/20' : 'border-l-amber-500 bg-amber-50/20') }}">
        <div>
            <div class="text-xs text-slate-400 mb-1">Status Penanganan Saat Ini:</div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($report['status'] === 'RESOLVED')
                    <span class="badge badge-success text-sm py-1">✓ Selesai Ditangani (RESOLVED)</span>
                @elseif($report['status'] === 'IN_PROGRESS')
                    <span class="badge badge-info text-sm py-1">⚙ Sedang Ditangani Satgas (IN PROGRESS)</span>
                @elseif($report['status'] === 'UNDER_REVIEW')
                    <span class="badge badge-warning text-sm py-1">🔍 Dalam Telaah Satgas (UNDER REVIEW)</span>
                @else
                    <span class="badge bg-purple-50 text-purple-700 text-sm py-1 font-semibold">📩 Laporan Terkirim (SUBMITTED)</span>
                @endif
                <span class="text-xs text-slate-500">• Kategori: <strong>{{ $report['category'] }}</strong></span>
                <span class="text-xs text-slate-500">• Prioritas: <strong>{{ $report['priority'] }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-xs text-slate-500">
                ID: <strong class="font-mono text-slate-700">{{ $report['id'] }}</strong>
            </div>
            @if(session('user_role') === 'admin')
                <button onclick="document.getElementById('admin-update-modal').classList.remove('hidden')" class="btn btn-primary btn-sm text-xs">
                    Tindak Lanjut Admin
                </button>
            @endif
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Report Details & Chronology --}}
        <div class="lg:col-span-2 space-y-6">
            @if(session('user_role') === 'admin')
                {{-- Reporter Info Box (Privileged for Admin) --}}
                <div class="card p-6 space-y-3 bg-amber-50/30 border-amber-200">
                    <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        Identitas Pelapor (Rahasia / Admin Only)
                    </h3>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400">Nama Pelapor:</span>
                            <div class="font-bold text-slate-800">{{ $report['reporter_name'] }}</div>
                        </div>
                        <div>
                            <span class="text-slate-400">Peran & NIM:</span>
                            <div class="font-medium text-slate-700">{{ $report['reporter_role'] }} ({{ $report['reporter_nim'] ?? '-' }})</div>
                        </div>
                        <div>
                            <span class="text-slate-400">Opsi Privasi:</span>
                            <div class="font-semibold text-slate-700">{{ ($report['is_anonymous'] ?? false) ? '🛡️ Anonim bagi Publik' : '👤 Terbuka' }}</div>
                        </div>
                        <div>
                            <span class="text-slate-400">Waktu Dibuat:</span>
                            <div class="font-medium text-slate-700">{{ $report['created_at'] }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                    Informasi & Kronologi Kejadian
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <div class="text-slate-400 mb-0.5">Tanggal Kejadian:</div>
                        <div class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($report['incident_date'])->translatedFormat('d F Y') }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 mb-0.5">Lokasi Insiden:</div>
                        <div class="font-semibold text-slate-800">{{ $report['location'] }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="text-slate-400 mb-0.5">Pihak yang Terlibat:</div>
                        <div class="font-medium text-slate-700">{{ $report['involved_parties'] ?? 'Dirahasiakan' }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-400 mb-1.5 font-semibold">Deskripsi Kronologi:</div>
                    <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                        {{ $report['description'] }}
                    </p>
                </div>

                <div>
                    <div class="text-xs text-slate-400 mb-1">Bukti Lampiran:</div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-700 flex items-center justify-between">
                        <span>📎 {{ $report['attachments'] ?? 'Tidak ada lampiran' }}</span>
                        <button onclick="showToast('Mengunduh lampiran bukti')" class="text-navy-600 font-semibold hover:underline">Unduh</button>
                    </div>
                </div>
            </div>

            {{-- Admin Response Box --}}
            @if(!empty($report['admin_note']))
                <div class="card p-6 bg-navy-50/50 border-navy-200 space-y-2">
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-wider">Tanggapan & Rekomendasi Satgas</h3>
                    <p class="text-sm text-navy-950 leading-relaxed">{{ $report['admin_note'] }}</p>
                </div>
            @endif
        </div>

        {{-- Right: Progressive Timeline --}}
        <div class="space-y-6">
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Riwayat Penanganan
                </h3>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($report['timeline'] ?? [] as $step)
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-navy-600 ring-4 ring-white"></span>
                            <div class="text-xs font-bold text-slate-800">{{ $step['title'] }}</div>
                            <div class="text-[11px] text-slate-400 mb-1">{{ $step['time'] }}</div>
                            <div class="text-xs text-slate-600 leading-relaxed">{{ $step['desc'] }}</div>
                        </div>
                    @empty
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-navy-600 ring-4 ring-white"></span>
                            <div class="text-xs font-bold text-slate-800">Laporan Terdaftar</div>
                            <div class="text-[11px] text-slate-400 mb-1">{{ $report['created_at'] }}</div>
                            <div class="text-xs text-slate-600">Laporan baru dibuat secara aman.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-100 text-[11px] text-slate-500 leading-relaxed">
                Satgas Kampus Aman berkomitmen menindaklanjuti setiap laporan secara profesional, independen, dan berkeadilan.
            </div>
        </div>
    </div>
</div>

@if(session('user_role') === 'admin')
{{-- Admin Update Status Modal --}}
<div id="admin-update-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-fade-in-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-800">Update Status Laporan #{{ $report['id'] }}</h3>
            <button onclick="document.getElementById('admin-update-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ url('/admin/reports/' . $report['id'] . '/update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="form-group">
                <label class="form-label text-xs">Ubah Status Penanganan</label>
                <select name="status" class="form-select" required>
                    <option value="SUBMITTED" {{ $report['status'] === 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED (Laporan Masuk Baru)</option>
                    <option value="UNDER_REVIEW" {{ $report['status'] === 'UNDER_REVIEW' ? 'selected' : '' }}>UNDER REVIEW (Dalam Telaah Satgas)</option>
                    <option value="IN_PROGRESS" {{ $report['status'] === 'IN_PROGRESS' ? 'selected' : '' }}>IN PROGRESS (Sedang Ditindaklanjuti)</option>
                    <option value="RESOLVED" {{ $report['status'] === 'RESOLVED' ? 'selected' : '' }}>RESOLVED (Selesai Ditangani)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label text-xs">Tingkat Prioritas</label>
                <select name="priority" class="form-select" required>
                    <option value="LOW" {{ $report['priority'] === 'LOW' ? 'selected' : '' }}>LOW (Rendah)</option>
                    <option value="MEDIUM" {{ $report['priority'] === 'MEDIUM' ? 'selected' : '' }}>MEDIUM (Sedang)</option>
                    <option value="HIGH" {{ $report['priority'] === 'HIGH' ? 'selected' : '' }}>HIGH (Tinggi)</option>
                    <option value="URGENT" {{ $report['priority'] === 'URGENT' ? 'selected' : '' }}>URGENT (Darurat - Segera)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label text-xs">Catatan / Rekomendasi Satgas</label>
                <textarea name="admin_note" rows="4" class="form-textarea text-xs" placeholder="Tulis catatan tindak lanjut atau arahan untuk pelapor...">{{ $report['admin_note'] ?? '' }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('admin-update-modal').classList.add('hidden')" class="btn btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn btn-primary text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
