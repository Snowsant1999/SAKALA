@extends('layouts.admin')

@section('title', 'Manajemen Reservasi & Konflik')
@section('page-title', 'Manajemen Reservasi & Konflik')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Summary Banner / Metric Badges --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-amber-500">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Menunggu Review (Pending)</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-red-500">
            <div class="stat-icon bg-red-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value text-red-600">{{ $conflictCount }} Group</div>
                <div class="stat-label font-semibold text-red-600">Terdeteksi Konflik Jadwal</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-emerald-600">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $approvedCount }}</div>
                <div class="stat-label">Telah Disetujui</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-slate-500">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $rejectedCount }}</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
    </div>

    {{-- Tabs Toolbar --}}
    <div class="card p-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            <a href="{{ url('/admin/reservations?tab=conflicts') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all shrink-0 flex items-center gap-2 {{ ($activeTab === 'conflicts') ? 'bg-red-600 text-white shadow-md' : 'text-red-700 bg-red-50 hover:bg-red-100' }}">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                ⚠ Tab Konflik ({{ $conflictCount }})
            </a>
            <a href="{{ url('/admin/reservations?tab=pending') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all shrink-0 {{ ($activeTab === 'pending') ? 'bg-navy-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Menunggu Review ({{ $pendingCount }})
            </a>
            <a href="{{ url('/admin/reservations?tab=approved') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all shrink-0 {{ ($activeTab === 'approved') ? 'bg-navy-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Disetujui ({{ $approvedCount }})
            </a>
            <a href="{{ url('/admin/reservations?tab=rejected') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all shrink-0 {{ ($activeTab === 'rejected') ? 'bg-navy-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Ditolak ({{ $rejectedCount }})
            </a>
            <a href="{{ url('/admin/reservations?tab=all') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold transition-all shrink-0 {{ ($activeTab === 'all') ? 'bg-navy-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Semua Riwayat
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- CONFLICTS VIEW (PRD Scenario C Demo) --}}
    @if($activeTab === 'conflicts')
        <div class="space-y-6">
            <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" /></svg>
                </div>
                <div class="text-xs text-amber-950 space-y-1">
                    <div class="font-bold text-sm text-amber-900">Sistem Deteksi Konflik Penggunaan Ruangan</div>
                    <p class="leading-relaxed text-amber-800">
                        Terdapat beberapa pengajuan yang menginginkan <strong>ruangan dan rentang waktu yang sama</strong>. Sesuai ketentuan, hanya satu pengajuan yang dapat disetujui. Ketika Anda menyetujui salah satu pengajuan, pengajuan bentrok lainnya akan <strong>otomatis ditolak oleh sistem</strong>.
                    </p>
                </div>
            </div>

            @forelse($conflicts as $group)
                @php $first = $group[0]; @endphp
                <div class="card overflow-hidden border-2 border-red-200">
                    <div class="bg-red-50/80 px-6 py-4 border-b border-red-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <span class="badge badge-danger text-xs font-bold uppercase">Konflik Terdeteksi</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $first['room_name'] }}</span>
                            <span class="text-xs text-slate-500">• {{ \Carbon\Carbon::parse($first['date'])->translatedFormat('d F Y') }} ({{ $first['time_formatted'] }} WITA)</span>
                        </div>
                        <span class="text-xs font-semibold text-red-700 bg-white px-2.5 py-1 rounded-full border border-red-200">
                            {{ count($group) }} Pengajuan Bentrok
                        </span>
                    </div>

                    {{-- Side by Side Comparison Grid --}}
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6 bg-slate-50/50">
                        @foreach($group as $index => $r)
                            <div class="card p-5 bg-white border border-slate-200 hover:border-navy-400 hover:shadow-md transition-all flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="badge badge-primary font-mono text-[11px]">{{ $r['id'] }}</span>
                                        <span class="badge badge-warning text-[10px]">Opsi #{{ $index + 1 }}</span>
                                    </div>

                                    <div>
                                        <h4 class="text-base font-bold text-slate-800">{{ $r['requester_name'] }}</h4>
                                        <div class="text-xs text-slate-500 font-medium">{{ $r['requester_role'] }} ({{ $r['requester_nim'] ?? '-' }})</div>
                                    </div>

                                    <div class="space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <div><span class="text-slate-400">Kelas / Prodi:</span> <strong class="text-slate-700">{{ $r['class'] }}</strong> • {{ $r['study_program'] }}</div>
                                        <div><span class="text-slate-400">Mata Kuliah:</span> <strong class="text-slate-700">{{ $r['course'] }}</strong></div>
                                        <div><span class="text-slate-400">Tujuan:</span> {{ $r['purpose'] }}</div>
                                        @if(!empty($r['notes']))
                                            <div><span class="text-slate-400">Catatan:</span> {{ $r['notes'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Decision Actions for this Option --}}
                                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <a href="{{ url('/reservations/' . $r['id']) }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">Detail Lengkap</a>
                                    
                                    <div class="flex items-center gap-2">
                                        {{-- Reject Button --}}
                                        <form action="{{ url('/admin/reservations/' . $r['id'] . '/reject') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary text-xs text-red-600 hover:bg-red-50 border-red-200">
                                                Tolak Opsi Ini
                                            </button>
                                        </form>

                                        {{-- Approve Button (Triggers auto conflict resolution) --}}
                                        <form action="{{ url('/admin/reservations/' . $r['id'] . '/approve') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="admin_note" value="Disetujui untuk {{ $r['requester_name'] }} ({{ $r['class'] }}). Slot waktu resmi dikunci.">
                                            <button type="submit" class="btn btn-success text-xs font-bold shadow-md">
                                                ✓ Setujui & Kunci Slot
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-emerald-500 opacity-60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <div class="text-base font-semibold text-slate-700 mb-1">Tidak Ada Konflik Jadwal</div>
                    <p class="text-sm text-slate-500">Semua pengajuan reservasi memiliki waktu dan ruangan yang terpisah.</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- STANDARD RESERVATIONS LIST VIEW --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID & Waktu Request</th>
                            <th>Pemohon</th>
                            <th>Ruangan & Lokasi</th>
                            <th>Jadwal Reservasi</th>
                            <th>Tujuan / Kegiatan</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $r)
                            <tr>
                                <td>
                                    <div class="font-mono text-xs font-bold text-navy-700">{{ $r['id'] }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $r['created_at'] }}</div>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-800 text-xs">{{ $r['requester_name'] }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $r['requester_role'] }} • {{ $r['class'] }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold text-slate-800 text-xs">{{ $r['room_name'] }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $r['building'] }} ({{ $r['floor'] }})</div>
                                </td>
                                <td>
                                    <div class="font-medium text-slate-700 text-xs">{{ \Carbon\Carbon::parse($r['date'])->translatedFormat('d M Y') }}</div>
                                    <div class="text-[11px] font-bold text-navy-600">{{ $r['time_formatted'] }} WITA</div>
                                </td>
                                <td>
                                    <div class="text-xs text-slate-700 font-medium line-clamp-1">{{ $r['purpose'] }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $r['course'] }}</div>
                                </td>
                                <td>
                                    @if($r['status'] === 'APPROVED')
                                        <span class="badge badge-success text-[10px]">APPROVED</span>
                                    @elseif($r['status'] === 'PENDING')
                                        <span class="badge badge-warning text-[10px]">PENDING</span>
                                    @else
                                        <span class="badge badge-danger text-[10px]">REJECTED</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($r['status'] === 'PENDING')
                                            <form action="{{ url('/admin/reservations/' . $r['id'] . '/approve') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm text-[11px]" title="Setujui">
                                                    ✓ Approve
                                                </button>
                                            </form>
                                            <form action="{{ url('/admin/reservations/' . $r['id'] . '/reject') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm text-[11px]" title="Tolak">
                                                    ✗ Reject
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ url('/reservations/' . $r['id']) }}" class="btn btn-secondary btn-sm text-[11px]">
                                                Detail
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400">
                                    Tidak ada data reservasi pada kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
