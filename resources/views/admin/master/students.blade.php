@extends('layouts.admin')

@section('title', 'Master Data Mahasiswa')
@section('page-title', 'Data Mahasiswa')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input id="studentSearch" type="text" placeholder="Cari Mahasiswa / NIM..." class="form-input text-xs pl-9">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            </div>
            <label class="sr-only" for="studentProgramFilter">Filter program studi</label>
            <select id="studentProgramFilter" class="form-input text-xs w-full sm:w-52">
                <option value="">Semua Program Studi</option>
                @foreach(collect($students)->pluck('program')->filter()->unique()->sort() as $program)
                    <option value="{{ $program }}">{{ $program }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="studentStatusFilter">Filter status</label>
            <select id="studentStatusFilter" class="form-input text-xs w-full sm:w-36">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
            <p id="studentResultCount" class="text-xs text-slate-500" aria-live="polite">{{ count($students) }} mahasiswa</p>
            <a href="{{ url('/admin/students/create') }}" class="btn btn-primary text-xs shrink-0">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Mahasiswa
            </a>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama Lengkap</th>
                        <th>Email Kampus</th>
                        <th>Program Studi</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                        <tr data-student-row data-program="{{ $s['program'] }}" data-status="{{ $s['status'] }}">
                            <td class="font-mono font-bold text-xs text-navy-700">{{ $s['nim'] }}</td>
                            <td class="font-bold text-slate-800 text-xs">{{ $s['name'] }}</td>
                            <td class="text-xs text-slate-500">{{ $s['email'] }}</td>
                            <td class="text-xs text-slate-700">{{ $s['program'] }}</td>
                            <td class="text-xs font-semibold text-slate-800">{{ $s['class'] ?? 'Belum tersedia' }}</td>
                            <td>
                                <span class="badge {{ $s['status'] === 'Aktif' ? 'badge-success' : 'badge-warning' }} text-[10px]">
                                    {{ $s['status'] }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ url('/admin/students/'.$s['id'].'/edit') }}" class="text-navy-600 hover:text-navy-800 text-xs font-semibold">Edit</a>
                                    <form method="POST" action="{{ url('/admin/students/'.$s['id']) }}" onsubmit="return confirm('Hapus data mahasiswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="studentEmptyState" @if(count($students) > 0) hidden @endif>
                        <td colspan="7" class="text-center text-xs text-slate-500 py-8">Tidak ada mahasiswa yang sesuai dengan filter.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const studentSearch = document.getElementById('studentSearch');
    const studentProgramFilter = document.getElementById('studentProgramFilter');
    const studentStatusFilter = document.getElementById('studentStatusFilter');
    const studentResultCount = document.getElementById('studentResultCount');
    const studentEmptyState = document.getElementById('studentEmptyState');
    const studentRows = [...document.querySelectorAll('[data-student-row]')];

    const filterStudents = () => {
        const query = studentSearch.value.trim().toLocaleLowerCase();
        const program = studentProgramFilter.value;
        const status = studentStatusFilter.value;
        let visibleCount = 0;

        studentRows.forEach((row) => {
            const matches = row.textContent.toLocaleLowerCase().includes(query)
                && (!program || row.dataset.program === program)
                && (!status || row.dataset.status === status);
            row.hidden = !matches;
            visibleCount += matches ? 1 : 0;
        });

        studentResultCount.textContent = `${visibleCount} mahasiswa`;
        studentEmptyState.hidden = visibleCount > 0;
    };

    studentSearch.addEventListener('input', filterStudents);
    studentProgramFilter.addEventListener('change', filterStudents);
    studentStatusFilter.addEventListener('change', filterStudents);
</script>
@endsection
