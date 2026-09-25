@extends('layouts.admin')

@section('title', 'Master Data Kelas')
@section('page-title', 'Data Kelas')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="relative w-full sm:w-72">
            <input type="text" placeholder="Cari kelas..." class="form-input text-xs pl-9">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
        </div>
        <button onclick="showToast('Fitur tambah kelas (Master Data)', 'info')" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Kelas
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5">
            <div class="text-xs text-slate-500 mb-1">Total Kelas</div>
            <div class="text-2xl font-bold text-navy-700">{{ count($classes) }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs text-slate-500 mb-1">Total Mahasiswa</div>
            <div class="text-2xl font-bold text-emerald-600">{{ collect($classes)->sum('total_students') }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs text-slate-500 mb-1">Rata-rata / Kelas</div>
            <div class="text-2xl font-bold text-indigo-600">{{ count($classes) > 0 ? round(collect($classes)->sum('total_students') / count($classes)) : 0 }}</div>
        </div>
    </div>

    {{-- Classes Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Program Studi</th>
                        <th>Tahun Akademik</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Mahasiswa</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $c)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                        {{ substr($c['name'], 0, 2) }}
                                    </div>
                                    <span class="font-bold text-xs text-slate-800">{{ $c['name'] }}</span>
                                </div>
                            </td>
                            <td class="text-xs text-slate-700">{{ $c['program'] }}</td>
                            <td><span class="badge text-[10px]" style="background: #f0f9ff; color: #0369a1;">{{ $c['academic_year'] }}</span></td>
                            <td class="text-xs text-slate-600">{{ $c['homeroom'] }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                        <div class="h-full rounded-full" style="width: {{ min(($c['total_students'] / 40) * 100, 100) }}%; background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700">{{ $c['total_students'] }}</span>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="showToast('Detail kelas {{ $c['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Detail</button>
                                    <button onclick="showToast('Edit kelas {{ $c['name'] }}', 'info')" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Edit</button>
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
