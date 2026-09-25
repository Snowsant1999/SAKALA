@extends('layouts.admin')

@section('title', 'Master Data Ruangan')
@section('page-title', 'Data Ruangan')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari ruangan..." class="form-input text-xs pl-9">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            </div>
            <select class="form-input text-xs">
                <option value="">Semua Tipe</option>
                <option>Kelas</option>
                <option>Laboratorium</option>
                <option>Aula</option>
            </select>
        </div>
        <button onclick="showToast('Fitur tambah ruangan (Master Data)', 'info')" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Ruangan
        </button>
    </div>

    {{-- Room Stats Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $statusCounts = collect($rooms)->groupBy('status')->map->count();
        @endphp
        <div class="card p-4 border-l-4" style="border-left-color: #6366f1;">
            <div class="text-[10px] text-slate-400 uppercase tracking-wider">Total</div>
            <div class="text-xl font-bold text-navy-700 mt-1">{{ count($rooms) }}</div>
        </div>
        <div class="card p-4 border-l-4" style="border-left-color: #10b981;">
            <div class="text-[10px] text-slate-400 uppercase tracking-wider">Kosong</div>
            <div class="text-xl font-bold text-emerald-600 mt-1">{{ $statusCounts['KOSONG'] ?? 0 }}</div>
        </div>
        <div class="card p-4 border-l-4" style="border-left-color: #f59e0b;">
            <div class="text-[10px] text-slate-400 uppercase tracking-wider">Digunakan</div>
            <div class="text-xl font-bold text-amber-600 mt-1">{{ $statusCounts['DIGUNAKAN'] ?? 0 }}</div>
        </div>
        <div class="card p-4 border-l-4" style="border-left-color: #ef4444;">
            <div class="text-[10px] text-slate-400 uppercase tracking-wider">Maintenance</div>
            <div class="text-xl font-bold text-red-600 mt-1">{{ $statusCounts['MAINTENANCE'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Rooms Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Ruangan</th>
                        <th>Gedung</th>
                        <th>Lantai</th>
                        <th>Kapasitas</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $r)
                        @php
                            $statusColors = [
                                'KOSONG' => 'badge-success',
                                'DIGUNAKAN' => 'badge-warning',
                                'RESERVED' => 'badge-info',
                                'MAINTENANCE' => 'badge-danger',
                            ];
                            $badgeClass = $statusColors[$r['status']] ?? 'badge-info';
                        @endphp
                        <tr>
                            <td class="font-mono font-bold text-xs text-navy-700">{{ $r['code'] }}</td>
                            <td class="font-bold text-xs text-slate-800">{{ $r['name'] }}</td>
                            <td class="text-xs text-slate-600">{{ $r['building'] }}</td>
                            <td class="text-xs text-slate-600">{{ $r['floor'] }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                                    <span class="text-xs font-bold text-slate-700">{{ $r['capacity'] }}</span>
                                </div>
                            </td>
                            <td><span class="badge text-[10px]" style="background: #f0f9ff; color: #0369a1;">{{ $r['type'] }}</span></td>
                            <td><span class="badge {{ $badgeClass }} text-[10px]">{{ $r['status'] }}</span></td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="showToast('Detail {{ $r['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Detail</button>
                                    <button onclick="showToast('Edit {{ $r['name'] }}', 'info')" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
