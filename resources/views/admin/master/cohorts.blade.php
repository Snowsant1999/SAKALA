@extends('layouts.admin')

@section('title', 'Master Data Rombongan')
@section('page-title', 'Data Rombongan')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-xs text-slate-500">Rombongan mahasiswa yang mengikuti beberapa kelas mata kuliah.</p>
        <a href="{{ url('/admin/cohorts/create') }}" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Rombongan
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Rombongan</th>
                        <th>Program Studi</th>
                        <th>Kelas Mata Kuliah</th>
                        <th>Mahasiswa</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cohorts as $cohort)
                        <tr>
                            <td class="font-bold text-xs text-slate-800">{{ $cohort['name'] }}</td>
                            <td class="text-xs text-slate-700">{{ $cohort['program'] }}</td>
                            <td class="text-xs text-slate-600">{{ $cohort['classes_count'] }}</td>
                            <td class="text-xs text-slate-600">{{ $cohort['students_count'] }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ url('/admin/cohorts/'.$cohort['id'].'/edit') }}" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</a>
                                    <form method="POST" action="{{ url('/admin/cohorts/'.$cohort['id']) }}" onsubmit="return confirm('Hapus rombongan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-400">Belum ada rombongan mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
