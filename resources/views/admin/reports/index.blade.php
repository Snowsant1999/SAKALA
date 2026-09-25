@extends('layouts.admin')

@section('title', 'Manajemen Laporan Kampus Aman')
@section('page-title', 'Manajemen Laporan Kampus Aman')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Metric Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card border-l-4 border-l-red-600">
            <div class="stat-icon bg-red-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value text-red-600">{{ $urgentCount }}</div>
                <div class="stat-label">Prioritas URGENT</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-orange-500">
            <div class="stat-icon bg-orange-500">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286ZM12 15h.008v.008H12V15Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value text-orange-600">{{ $highCount }}</div>
                <div class="stat-label">Prioritas TINGGI</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-sky-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $inProgressCount }}</div>
                <div class="stat-label">Dalam Penanganan</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-purple-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $submittedCount }}</div>
                <div class="stat-label">Laporan Masuk Baru</div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card p-4">
        <form method="GET" action="{{ url('/admin/reports') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500">Status:</label>
                <select name="status" class="form-select text-xs py-1.5 w-44" onchange="this.form.submit()">
                    <option value="all" {{ ($statusFilter === 'all') ? 'selected' : '' }}>Semua Status</option>
                    <option value="submitted" {{ ($statusFilter === 'submitted') ? 'selected' : '' }}>Submitted (Baru)</option>
                    <option value="under_review" {{ ($statusFilter === 'under_review') ? 'selected' : '' }}>Under Review</option>
                    <option value="in_progress" {{ ($statusFilter === 'in_progress') ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ ($statusFilter === 'resolved') ? 'selected' : '' }}>Resolved (Selesai)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500">Prioritas:</label>
                <select name="priority" class="form-select text-xs py-1.5 w-40" onchange="this.form.submit()">
                    <option value="all" {{ ($priorityFilter === 'all') ? 'selected' : '' }}>Semua Prioritas</option>
                    <option value="urgent" {{ ($priorityFilter === 'urgent') ? 'selected' : '' }}>URGENT (Darurat)</option>
                    <option value="high" {{ ($priorityFilter === 'high') ? 'selected' : '' }}>HIGH (Tinggi)</option>
                    <option value="medium" {{ ($priorityFilter === 'medium') ? 'selected' : '' }}>MEDIUM (Sedang)</option>
                    <option value="low" {{ ($priorityFilter === 'low') ? 'selected' : '' }}>LOW (Rendah)</option>
                </select>
            </div>

            @if($statusFilter !== 'all' || $priorityFilter !== 'all')
                <a href="{{ url('/admin/reports') }}" class="btn btn-secondary text-xs py-1.5">Reset Filter</a>
            @endif
        </form>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Reports Management Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID & Tanggal</th>
                        <th>Pelapor (Privat)</th>
                        <th>Kategori & Lokasi</th>
                        <th>Kronologi Singkat</th>
                        <th>Prioritas</th>
                        <th>Status Penanganan</th>
                        <th class="text-right">Aksi Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $r)
                        <tr>
                            <td>
                                <div class="font-mono text-xs font-bold text-navy-700">{{ $r['id'] }}</div>
                                <div class="text-[11px] text-slate-400">{{ $r['created_at'] }}</div>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 text-xs">{{ $r['reporter_name'] }}</div>
                                <div class="text-[11px] text-slate-500">{{ $r['reporter_role'] }} ({{ $r['reporter_nim'] ?? '-' }})</div>
                            </td>
                            <td>
                                <span class="badge badge-primary text-[10px] font-semibold">{{ $r['category'] }}</span>
                                <div class="text-[11px] text-slate-500 mt-1">📍 {{ $r['location'] }}</div>
                            </td>
                            <td>
                                <div class="text-xs text-slate-700 font-medium line-clamp-2 max-w-xs">{{ $r['description'] }}</div>
                            </td>
                            <td>
                                @if($r['priority'] === 'URGENT')
                                    <span class="badge badge-danger text-[10px] font-bold">URGENT</span>
                                @elseif($r['priority'] === 'HIGH')
                                    <span class="badge badge-warning text-[10px] font-bold">HIGH</span>
                                @elseif($r['priority'] === 'MEDIUM')
                                    <span class="badge badge-info text-[10px] font-semibold">MEDIUM</span>
                                @else
                                    <span class="badge badge-gray text-[10px]">LOW</span>
                                @endif
                            </td>
                            <td>
                                @if($r['status'] === 'RESOLVED')
                                    <span class="badge badge-success text-[10px]">RESOLVED</span>
                                @elseif($r['status'] === 'IN_PROGRESS')
                                    <span class="badge badge-info text-[10px]">IN PROGRESS</span>
                                @elseif($r['status'] === 'UNDER_REVIEW')
                                    <span class="badge badge-warning text-[10px]">UNDER REVIEW</span>
                                @else
                                    <span class="badge bg-purple-50 text-purple-700 text-[10px]">SUBMITTED</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ url('/reports/' . $r['id']) }}" class="btn btn-secondary btn-sm text-[11px]">
                                        Detail
                                    </a>
                                    <button onclick="openUpdateModal('{{ $r['id'] }}', '{{ $r['status'] }}', '{{ $r['priority'] }}', '{{ addslashes($r['admin_note'] ?? '') }}')" class="btn btn-primary btn-sm text-[11px]">
                                        Tindak Lanjut
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                Tidak ada laporan yang ditemukan pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Update Status & Priority Modal --}}
<div id="update-report-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-fade-in-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-800">Tindak Lanjut & Update Status Laporan</h3>
            <button onclick="document.getElementById('update-report-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="update-report-form" action="" method="POST" class="space-y-4">
            @csrf
            <div class="form-group">
                <label class="form-label text-xs">Ubah Status Penanganan</label>
                <select name="status" id="modal-status" class="form-select" required>
                    <option value="SUBMITTED">SUBMITTED (Laporan Diterima)</option>
                    <option value="UNDER_REVIEW">UNDER_REVIEW (Sedang Ditelaah)</option>
                    <option value="IN_PROGRESS">IN_PROGRESS (Dalam Proses Investigasi/Mediasi)</option>
                    <option value="RESOLVED">RESOLVED (Kasus Selesai Ditangani)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label text-xs">Tingkat Prioritas</label>
                <select name="priority" id="modal-priority" class="form-select" required>
                    <option value="LOW">LOW (Rendah)</option>
                    <option value="MEDIUM">MEDIUM (Sedang)</option>
                    <option value="HIGH">HIGH (Tinggi)</option>
                    <option value="URGENT">URGENT (Darurat / Butuh Intervensi Segera)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label text-xs">Catatan Tindak Lanjut / Respon untuk Pelapor</label>
                <textarea name="admin_note" id="modal-admin-note" rows="3" class="form-input" placeholder="Tuliskan perkembangan hasil investigasi atau jadwal pertemuan klarifikasi..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('update-report-modal').classList.add('hidden')" class="btn btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn btn-primary text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openUpdateModal(reportId, status, priority, adminNote) {
        document.getElementById('update-report-form').action = '{{ url("/admin/reports") }}/' + reportId + '/update';
        document.getElementById('modal-status').value = status;
        document.getElementById('modal-priority').value = priority;
        document.getElementById('modal-admin-note').value = adminNote || '';
        document.getElementById('update-report-modal').classList.remove('hidden');
    }
</script>
@endsection
