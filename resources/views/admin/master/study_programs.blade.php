@extends('layouts.admin')

@section('title', 'Master Data Program Studi')
@section('page-title', 'Data Program Studi')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-500 font-medium">Politeknik Negeri Samarinda — Daftar Program Studi</div>
        <button onclick="showToast('Fitur tambah program studi baru', 'info')" class="btn btn-primary text-xs">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Program Studi
        </button>
    </div>

    {{-- Study Programs Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Program Studi</th>
                        <th>Jurusan</th>
                        <th>Jenjang</th>
                        <th>Akreditasi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $p)
                        <tr>
                            <td class="font-mono font-bold text-xs text-navy-700">{{ $p['code'] }}</td>
                            <td class="font-bold text-slate-800 text-xs">{{ $p['name'] }}</td>
                            <td class="text-xs text-slate-700">{{ $p['department'] }}</td>
                            <td class="text-xs text-slate-600">{{ $p['degree'] }}</td>
                            <td>
                                <span class="badge badge-success text-[10px]">{{ $p['accreditation'] }}</span>
                            </td>
                            <td class="text-right">
                                <button onclick="showToast('Edit prodi {{ $p['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
