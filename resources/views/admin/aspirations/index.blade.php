@extends('layouts.admin')

@section('title', 'Manajemen Layanan Aspirasi & Sarpras')
@section('page-title', 'Manajemen Layanan Aspirasi & Sarana Prasarana')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Metric Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card border-l-4 border-l-navy-600">
            <div class="stat-icon bg-navy-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.83M11.42 15.17l-3.96 3.96a2.653 2.653 0 01-3.75-3.75l3.96-3.96m5.75 3.75l-5.75-5.75M10.5 4.5l3 3m-3-3l-3 3m3-3v8.25" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value text-navy-700">{{ $totalCount }}</div>
                <div class="stat-label">Total Aspirasi</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-amber-500">
            <div class="stat-icon bg-amber-500">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value text-amber-600">{{ $pendingCount }}</div>
                <div class="stat-label">Menunggu Respon</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-sky-500">
            <div class="stat-icon bg-sky-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value text-sky-600">{{ $processingCount }}</div>
                <div class="stat-label">Sedang Ditindaklanjuti</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-emerald-500">
            <div class="stat-icon bg-emerald-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value text-emerald-600">{{ $resolvedCount }}</div>
                <div class="stat-label">Selesai Ditangani</div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card p-4">
        <form method="GET" action="{{ url('/admin/aspirations') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500">Status:</label>
                <select name="status" class="form-select text-xs py-1.5 w-44" onchange="this.form.submit()">
                    <option value="all" {{ ($statusFilter === 'all') ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ ($statusFilter === 'pending') ? 'selected' : '' }}>Pending (Baru)</option>
                    <option value="processing" {{ ($statusFilter === 'processing') ? 'selected' : '' }}>Diproses Teknisi</option>
                    <option value="resolved" {{ ($statusFilter === 'resolved') ? 'selected' : '' }}>Selesai</option>
                    <option value="dismissed" {{ ($statusFilter === 'dismissed') ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500">Kategori:</label>
                <select name="category" class="form-select text-xs py-1.5 w-48" onchange="this.form.submit()">
                    <option value="all" {{ ($categoryFilter === 'all') ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="Kerusakan Fasilitas" {{ ($categoryFilter === 'Kerusakan Fasilitas') ? 'selected' : '' }}>Kerusakan Fasilitas</option>
                    <option value="Sarana Kelas" {{ ($categoryFilter === 'Sarana Kelas') ? 'selected' : '' }}>Sarana Kelas</option>
                    <option value="Koneksi Internet" {{ ($categoryFilter === 'Koneksi Internet') ? 'selected' : '' }}>Koneksi Internet</option>
                    <option value="Kebersihan" {{ ($categoryFilter === 'Kebersihan') ? 'selected' : '' }}>Kebersihan</option>
                </select>
            </div>

            @if($statusFilter !== 'all' || $categoryFilter !== 'all')
                <a href="{{ url('/admin/aspirations') }}" class="btn btn-secondary text-xs py-1.5">Reset Filter</a>
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

    {{-- Aspirations Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID & Tanggal</th>
                        <th>Pelapor</th>
                        <th>Kategori & Lokasi</th>
                        <th>Deskripsi Kendala</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aspirations as $a)
                        <tr>
                            <td>
                                <div class="font-mono text-xs font-bold text-navy-700">{{ $a['id'] }}</div>
                                <div class="text-[11px] text-slate-400">{{ $a['created_at'] }}</div>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 text-xs">{{ $a['reporter_name'] }}</div>
                                <div class="text-[11px] text-slate-500">{{ $a['reporter_nim'] }}</div>
                            </td>
                            <td>
                                <span class="badge badge-primary text-[10px] font-semibold">{{ $a['category'] }}</span>
                                <div class="text-[11px] text-slate-500 mt-1">📍 {{ $a['location'] }}</div>
                            </td>
                            <td>
                                <div class="text-xs text-slate-700 font-medium line-clamp-2 max-w-xs">{{ $a['description'] }}</div>
                            </td>
                            <td>
                                @if($a['status'] === 'SELESAI')
                                    <span class="badge badge-success text-[10px]">SELESAI</span>
                                @elseif($a['status'] === 'DIPROSES')
                                    <span class="badge badge-info text-[10px]">DIPROSES</span>
                                @elseif($a['status'] === 'DITOLAK')
                                    <span class="badge badge-danger text-[10px]">DITOLAK</span>
                                @else
                                    <span class="badge badge-warning text-[10px]">PENDING</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ url('/aspirations/' . $a['raw_id']) }}" class="btn btn-secondary btn-sm text-[11px]">
                                        Detail
                                    </a>
                                    <button type="button" data-aspiration-update data-id="{{ $a['raw_id'] }}" data-status="{{ $a['status'] }}" data-note="{{ $a['admin_notes'] ?? '' }}" class="btn btn-primary btn-sm text-[11px]">
                                        Update Status
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400">
                                Tidak ada aspirasi / keluhan yang ditemukan pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Update Status Modal --}}
<div id="update-aspiration-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-fade-in-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-800">Tindak Lanjut & Status Keluhan Sarpras</h3>
            <button onclick="document.getElementById('update-aspiration-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="update-aspiration-form" action="" method="POST" class="space-y-4">
            @csrf
            <div class="form-group">
                <label class="form-label text-xs">Ubah Status</label>
                <select name="status" id="modal-asp-status" class="form-select" required>
                    <option value="PENDING">PENDING (Menunggu Tindak Lanjut)</option>
                    <option value="DIPROSES">DIPROSES (Teknisi Sedang Menangani)</option>
                    <option value="SELESAI">SELESAI (Perbaikan Selesai)</option>
                    <option value="DITOLAK">DITOLAK (Dibatalkan / Tidak Sesuai)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label text-xs">Catatan Tindak Lanjut / Respon Sarpras</label>
                <textarea name="admin_notes" id="modal-asp-notes" rows="3" class="form-input" placeholder="Tuliskan catatan perbaikan fasilitas untuk pelapor..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('update-aspiration-modal').classList.add('hidden')" class="btn btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn btn-primary text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-aspiration-update]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('update-aspiration-form').action = '{{ url("/admin/aspirations") }}/' + button.dataset.id + '/update';
            document.getElementById('modal-asp-status').value = button.dataset.status;
            document.getElementById('modal-asp-notes').value = button.dataset.note || '';
            document.getElementById('update-aspiration-modal').classList.remove('hidden');
        });
    });
</script>
@endsection
