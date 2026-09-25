@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Materi - ' . $course['name'])
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ url('/courses/' . $course['id']) }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    <span>Materi — {{ $course['name'] }}</span>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Course Header Banner --}}
    <div class="rounded-2xl p-6 text-white relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #1e2788 0%, #2a3dea 60%, #3d5af5 100%);">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-white/20">{{ $course['code'] }}</span>
                    <span class="text-xs text-indigo-100">Kelas {{ $course['class'] }} • {{ $course['lecturer'] }}</span>
                </div>
                <h1 class="text-2xl font-extrabold">{{ $course['name'] }}</h1>
            </div>

            @if(session('user_role') === 'lecturer' || session('user_role') === 'admin')
                <button onclick="document.getElementById('add-material-modal').classList.remove('hidden')" class="btn bg-white text-navy-700 hover:bg-slate-100 shadow-md font-semibold">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Materi Baru
                </button>
            @endif
        </div>
    </div>

    {{-- Sub Navigation Tabs --}}
    <div class="flex border-b border-slate-200 gap-6">
        <a href="{{ url('/courses/' . $course['id']) }}" class="pb-3 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            Overview
        </a>
        <a href="{{ url('/courses/' . $course['id'] . '/materials') }}" class="pb-3 text-sm font-bold text-navy-600 border-b-2 border-navy-600 flex items-center gap-2">
            Materi Perkuliahan ({{ count($materials) }})
        </a>
        <a href="{{ url('/courses/' . $course['id'] . '/assignments') }}" class="pb-3 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            Tugas Kuliah
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Materials List --}}
    <div class="space-y-4">
        @forelse($materials as $index => $m)
            <div class="card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 hover:shadow-md transition-all">
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm shrink-0 border border-red-100">
                        PDF
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="badge badge-gray text-[11px]">Pertemuan {{ $index + 1 }}</span>
                            <span class="text-xs text-slate-400">Diunggah: {{ $m['uploaded_at'] }}</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mb-1">{{ $m['title'] }}</h3>
                        <p class="text-sm text-slate-500 mb-2 leading-relaxed">{{ $m['description'] }}</p>
                        <div class="text-xs text-slate-400 flex items-center gap-2">
                            <span>📄 {{ $m['file_name'] }}</span>
                            <span>•</span>
                            <span>{{ $m['file_size'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                    <button onclick="showToast('Mengunduh materi: {{ $m['file_name'] }}', 'info')" class="btn btn-secondary text-xs">
                        <svg class="w-4 h-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Unduh Materi
                    </button>

                    @if(session('user_role') === 'lecturer' || session('user_role') === 'admin')
                        <form action="{{ url('/courses/' . $course['id'] . '/materials/' . $m['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary text-xs text-red-600 hover:bg-red-50 border-red-200" title="Hapus Materi">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                <div class="text-base font-semibold text-slate-700 mb-1">Belum ada materi perkuliahan</div>
                <p class="text-sm text-slate-500">Dosen belum mengunggah berkas materi untuk mata kuliah ini.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Add Material Modal (for Lecturer) --}}
<div id="add-material-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-fade-in-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-lg font-bold text-slate-800">Unggah Materi Perkuliahan</h3>
            <button onclick="document.getElementById('add-material-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ url('/courses/' . $course['id'] . '/materials') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="mat_title">Judul Materi</label>
                <input type="text" name="title" id="mat_title" class="form-input" placeholder="Contoh: Pertemuan 4 - Database Migration" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="mat_desc">Deskripsi Singkat</label>
                <textarea name="description" id="mat_desc" rows="3" class="form-input" placeholder="Jelaskan ringkasan materi atau instruksi bacaan..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Berkas Materi (Mock Upload)</label>
                <input type="text" name="file_name" class="form-input" placeholder="Nama berkas PDF / PPT" value="Modul_Kuliah_{{ date('dmy') }}.pdf">
                <p class="text-[11px] text-slate-400 mt-1">Simulasi upload file dokumen PDF/PPT/ZIP.</p>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('add-material-modal').classList.add('hidden')" class="btn btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn btn-primary text-xs">Unggah Materi</button>
            </div>
        </form>
    </div>
</div>
@endsection
