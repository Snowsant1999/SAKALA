@extends('layouts.admin')

@section('title', 'Master Data Jurusan')
@section('page-title', 'Data Jurusan')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-500 font-medium">Politeknik Negeri Samarinda — Daftar Jurusan</div>
        <button onclick="showToast('Fitur tambah jurusan baru', 'info')" class="btn btn-primary text-xs">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Jurusan
        </button>
    </div>

    {{-- Departments Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Jurusan</th>
                        <th>Ketua Jurusan</th>
                        <th>Jumlah Program Studi</th>
                        <th>Estimasi Mahasiswa</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $d)
                        <tr>
                            <td class="font-mono font-bold text-xs text-navy-700">{{ $d['code'] }}</td>
                            <td class="font-bold text-slate-800 text-xs">{{ $d['name'] }}</td>
                            <td class="text-xs text-slate-700">{{ $d['head'] }}</td>
                            <td class="text-xs text-slate-600">{{ $d['programs_count'] }} Prodi</td>
                            <td class="text-xs text-slate-600">{{ $d['students_count'] }} Orang</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="showToast('Edit data jurusan {{ $d['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</button>
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
