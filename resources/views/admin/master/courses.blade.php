@extends('layouts.admin')

@section('title', 'Master Data Mata Kuliah')
@section('page-title', 'Data Mata Kuliah')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-500 font-medium">Kurikulum & Daftar Mata Kuliah Aktif</div>
        <button onclick="showToast('Fitur tambah mata kuliah baru', 'info')" class="btn btn-primary text-xs">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Mata Kuliah
        </button>
    </div>

    {{-- Courses Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Dosen Pengampu</th>
                        <th>Program Studi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $c)
                        <tr>
                            <td class="font-mono font-bold text-xs text-navy-700">{{ $c['code'] }}</td>
                            <td class="font-bold text-slate-800 text-xs">{{ $c['name'] }}</td>
                            <td class="text-xs text-slate-600">{{ $c['sks'] }} SKS</td>
                            <td class="text-xs text-slate-600">Semester {{ $c['semester'] }}</td>
                            <td class="text-xs text-slate-700 font-medium">{{ $c['lecturer'] }}</td>
                            <td class="text-xs text-slate-500">{{ $c['study_program'] }}</td>
                            <td class="text-right">
                                <button onclick="showToast('Edit MK {{ $c['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
