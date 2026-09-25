@extends('layouts.admin')

@section('title', 'Manajemen Users')
@section('page-title', 'Users')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="relative w-full sm:w-72">
            <input type="text" placeholder="Cari user..." class="form-input text-xs pl-9">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
        </div>
        <div class="flex items-center gap-2">
            <select class="form-input text-xs" id="roleFilter">
                <option value="">Semua Role</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
                <option value="admin">Admin</option>
            </select>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Program Studi</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">{{ strtoupper(substr($s['name'], 0, 1)) }}</div>
                                    <div>
                                        <div class="font-bold text-xs text-slate-800">{{ $s['name'] }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $s['nim'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-xs text-slate-500">{{ $s['email'] }}</td>
                            <td><span class="badge text-[10px]" style="background: #eef2ff; color: #4338ca;">Mahasiswa</span></td>
                            <td class="text-xs text-slate-700">{{ $s['program'] }}</td>
                            <td><span class="badge {{ $s['status'] === 'Aktif' ? 'badge-success' : 'badge-warning' }} text-[10px]">{{ $s['status'] }}</span></td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="showToast('Edit user {{ $s['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @foreach($lecturers as $l)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background: linear-gradient(135deg, #059669, #10b981);">{{ strtoupper(substr($l['name'], 0, 1)) }}</div>
                                    <div>
                                        <div class="font-bold text-xs text-slate-800">{{ $l['name'] }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $l['nidn'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-xs text-slate-500">{{ $l['email'] }}</td>
                            <td><span class="badge text-[10px]" style="background: #ecfdf5; color: #059669;">Dosen</span></td>
                            <td class="text-xs text-slate-700">{{ $l['department'] }}</td>
                            <td><span class="badge badge-success text-[10px]">Aktif</span></td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="showToast('Edit user {{ $l['name'] }}', 'info')" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</button>
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
