@extends('layouts.admin')

@section('title', 'Master Data Dosen')
@section('page-title', 'Data Dosen')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="relative w-full sm:w-72">
            <input type="text" placeholder="Cari Dosen / NIDN..." class="form-input text-xs pl-9">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
        </div>

        <button onclick="showToast('Fitur tambah dosen (Master Data)', 'info')" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Dosen
        </button>
    </div>

    {{-- Lecturers Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NIDN</th>
                        <th>Nama Dosen & Gelar</th>
                        <th>Email Kampus</th>
                        <th>Jurusan</th>
                        <th>Bidang Keahlian</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lecturers as $l)
                        <tr>
                            <td class="font-mono font-bold text-xs text-navy-700">{{ $l['nidn'] }}</td>
                            <td class="font-bold text-slate-800 text-xs">{{ $l['name'] }}</td>
                            <td class="text-xs text-slate-500">{{ $l['email'] }}</td>
                            <td class="text-xs text-slate-700">{{ $l['department'] }}</td>
                            <td class="text-xs text-slate-600">{{ $l['specialization'] }}</td>
                            <td>
                                <span class="badge badge-success text-[10px]">{{ $l['status'] }}</span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="showToast('Edit dosen {{ $l['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</button>
                                    <button onclick="showToast('Hapus dosen {{ $l['name'] }}', 'warning')" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
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
