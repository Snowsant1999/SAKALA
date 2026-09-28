@extends('layouts.admin')

@section('title', 'Master Data Mata Kuliah')
@section('page-title', 'Data Mata Kuliah')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <input id="courseSearch" type="search" placeholder="Cari mata kuliah / kode..." class="form-input text-xs sm:max-w-xs">
        <a href="{{ url('/admin/courses/create') }}" class="btn btn-primary text-xs">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Mata Kuliah
        </a>
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
                        <tr data-course-row>
                            <td class="font-mono font-bold text-xs text-navy-700">{{ $c['code'] }}</td>
                            <td class="font-bold text-slate-800 text-xs">{{ $c['name'] }}</td>
                            <td class="text-xs text-slate-600">{{ $c['sks'] }} SKS</td>
                            <td class="text-xs text-slate-600">Semester {{ $c['semester'] }}</td>
                            <td class="text-xs text-slate-700 font-medium">{{ $c['lecturer'] }}</td>
                            <td class="text-xs text-slate-500">{{ $c['study_program'] }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ url('/admin/courses/'.$c['id'].'/edit') }}" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</a>
                                    <form method="POST" action="{{ url('/admin/courses/'.$c['id']) }}" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                                    </form>
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

@section('scripts')
<script>
    const courseSearch = document.getElementById('courseSearch');
    courseSearch.addEventListener('input', () => {
        const query = courseSearch.value.trim().toLocaleLowerCase();
        document.querySelectorAll('[data-course-row]').forEach((row) => {
            row.hidden = !row.textContent.toLocaleLowerCase().includes(query);
        });
    });
</script>
@endsection
