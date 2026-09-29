@extends('layouts.admin')

@section('title', 'Master Data Kelas')
@section('page-title', 'Data Kelas')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="relative w-full sm:w-72">
            <input id="classSearch" type="text" placeholder="Cari kelas..." class="form-input text-xs pl-9">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
        </div>
        <a href="{{ url('/admin/classes/create') }}" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Mata Kuliah
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5">
            <div class="text-xs text-slate-500 mb-1">Total Rombongan</div>
            <div class="text-2xl font-bold text-navy-700">{{ count($classes) }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs text-slate-500 mb-1">Total Mahasiswa</div>
            <div class="text-2xl font-bold text-emerald-600">{{ collect($classes)->sum('total_students') }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs text-slate-500 mb-1">Rata-rata / Rombongan</div>
            <div class="text-2xl font-bold text-indigo-600">{{ count($classes) > 0 ? round(collect($classes)->sum('total_students') / count($classes)) : 0 }}</div>
        </div>
    </div>

    {{-- Classes Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Rombongan</th>
                        <th>Program Studi</th>
                        <th>Jumlah Mahasiswa</th>
                        <th>Tahun Akademik</th>
                        <th>Mata Kuliah</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $c)
                        <tr data-class-row data-cohort-row>
                            <td class="font-bold text-xs text-slate-800">{{ $c['name'] }}</td>
                            <td class="text-xs text-slate-700">{{ $c['program'] }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                        <div class="h-full rounded-full" style="width: {{ min(($c['total_students'] / 40) * 100, 100) }}%; background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700">{{ $c['total_students'] }}</span>
                                </div>
                            </td>
                            <td>
                                @forelse($c['academic_years'] as $academicYear)
                                    <span class="badge text-[10px]" style="background: #f0f9ff; color: #0369a1;">{{ $academicYear }}</span>
                                @empty
                                    <span class="text-xs text-slate-400">Belum ditentukan</span>
                                @endforelse
                            </td>
                            <td>
                                @if($c['course_classes']->isEmpty())
                                    <span class="text-xs text-slate-400">Belum ada mata kuliah</span>
                                @else
                                    <details>
                                        <summary class="cursor-pointer text-xs font-semibold text-navy-700">
                                            {{ $c['course_classes']->count() }} mata kuliah
                                        </summary>
                                        <div class="mt-2 space-y-2">
                                            @foreach($c['course_classes'] as $courseClass)
                                                <div class="flex items-start justify-between gap-3 border-t border-slate-100 pt-2">
                                                    <div>
                                                        <div class="text-xs font-semibold text-slate-800">{{ $courseClass['course'] }}</div>
                                                        <div class="text-[11px] text-slate-500">{{ $courseClass['lecturer'] }} · {{ $courseClass['academic_year'] }}</div>
                                                    </div>
                                                    <div class="flex shrink-0 items-center gap-2">
                                                        <a href="{{ url('/admin/classes/'.$courseClass['id'].'/edit') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Edit</a>
                                                        <form method="POST" action="{{ url('/admin/classes/'.$courseClass['id']) }}" onsubmit="return confirm('Hapus mata kuliah dari rombongan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ url('/admin/classes/create?cohort_id='.$c['id']) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Tambah Mata Kuliah</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-slate-500">Belum ada rombongan yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const classSearch = document.getElementById('classSearch');
    classSearch.addEventListener('input', () => {
        const query = classSearch.value.trim().toLocaleLowerCase();
        document.querySelectorAll('[data-class-row]').forEach((row) => {
            row.hidden = !row.textContent.toLocaleLowerCase().includes(query);
        });
    });
</script>
@endsection
