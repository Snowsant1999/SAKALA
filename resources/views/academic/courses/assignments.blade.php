@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Tugas - ' . $course['name'])
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ url('/courses/' . $course['id']) }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    <span>Tugas — {{ $course['name'] }}</span>
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
                <button onclick="document.getElementById('add-assignment-modal').classList.remove('hidden')" class="btn bg-white text-navy-700 hover:bg-slate-100 shadow-md font-semibold">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Terbitkan Tugas Baru
                </button>
            @endif
        </div>
    </div>

    {{-- Sub Navigation Tabs --}}
    <div class="flex border-b border-slate-200 gap-6">
        <a href="{{ url('/courses/' . $course['id']) }}" class="pb-3 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            Overview
        </a>
        <a href="{{ url('/courses/' . $course['id'] . '/materials') }}" class="pb-3 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            Materi Perkuliahan
        </a>
        <a href="{{ url('/courses/' . $course['id'] . '/assignments') }}" class="pb-3 text-sm font-bold text-navy-600 border-b-2 border-navy-600 flex items-center gap-2">
            Tugas Kuliah ({{ count($assignments) }})
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Assignments List --}}
    <div class="space-y-4">
        @forelse($assignments as $a)
            @php
                $submission = $a['submissions'][$userEmail] ?? null;
                $isSubmitted = $submission && ($submission['status'] === 'SUBMITTED');
            @endphp
            <div class="card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-md transition-all border-l-4 {{ $isSubmitted ? 'border-l-emerald-500' : 'border-l-amber-500' }}">
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-12 h-12 rounded-xl {{ $isSubmitted ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} flex items-center justify-center font-bold text-sm shrink-0 border">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            @if($isSubmitted)
                                <span class="badge badge-success text-[11px]">✓ Terkumpul</span>
                            @else
                                <span class="badge badge-warning text-[11px]">⏳ Belum Dikerjakan</span>
                            @endif
                            <span class="text-xs text-slate-400">Batas Waktu: <strong>{{ $a['deadline_formatted'] ?? $a['deadline'] }}</strong></span>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mb-1">{{ $a['title'] }}</h3>
                        <p class="text-sm text-slate-500 mb-3 leading-relaxed">{{ $a['description'] }}</p>
                        
                        @if($isSubmitted)
                            <div class="bg-emerald-50/60 p-3 rounded-lg border border-emerald-100 text-xs text-emerald-900 space-y-1">
                                <div><strong>Berkas Dikirim:</strong> {{ $submission['file_name'] }} ({{ $submission['submitted_at'] }})</div>
                                <div><strong>Status Nilai:</strong> {{ $submission['grade'] ?? 'Belum Dinilai' }}</div>
                                @if(!empty($submission['feedback']))
                                    <div class="text-slate-600 mt-1 italic">"{{ $submission['feedback'] }}"</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="shrink-0 self-end md:self-center">
                    @if(!$isSubmitted)
                        <button onclick="openSubmitModal('{{ $a['id'] }}', '{{ addslashes($a['title']) }}')" class="btn btn-primary text-xs">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                            Kumpulkan Tugas
                        </button>
                    @else
                        <button onclick="openSubmitModal('{{ $a['id'] }}', '{{ addslashes($a['title']) }}')" class="btn btn-secondary text-xs">
                            Kirim Ulang
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                <div class="text-base font-semibold text-slate-700 mb-1">Tidak ada tugas aktif</div>
                <p class="text-sm text-slate-500">Semua tugas perkuliahan telah diselesaikan atau belum diterbitkan.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Submit Assignment Modal (for Student) --}}
<div id="submit-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-fade-in-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-lg font-bold text-slate-800">Kumpulkan Tugas</h3>
            <button onclick="document.getElementById('submit-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="submit-assignment-form" action="" method="POST">
            @csrf
            <div class="mb-4">
                <div class="text-xs text-slate-400">Judul Tugas:</div>
                <div id="modal-task-title" class="text-sm font-bold text-slate-800"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Berkas Jawaban (Mock File Upload)</label>
                <input type="text" name="file_name" class="form-input" placeholder="Contoh: Tugas_PemrogramanWeb_Andi.zip" value="Tugas_{{ str_replace(' ', '_', $course['name']) }}_Andi.zip" required>
                <p class="text-[11px] text-slate-400 mt-1">Simulasi upload file format ZIP, PDF, atau DOCX.</p>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="form-input" placeholder="Tuliskan catatan untuk dosen pengampu jika ada..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('submit-modal').classList.add('hidden')" class="btn btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn btn-primary text-xs">Kirim Jawaban Tugas</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Assignment Modal (for Lecturer) --}}
<div id="add-assignment-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-fade-in-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-lg font-bold text-slate-800">Terbitkan Tugas Baru</h3>
            <button onclick="document.getElementById('add-assignment-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ url('/courses/' . $course['id'] . '/assignments') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="asg_title">Judul Tugas</label>
                <input type="text" name="title" id="asg_title" class="form-input" placeholder="Contoh: Tugas 3 - Implementasi Middleware" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="asg_deadline">Tenggat Waktu (Deadline)</label>
                <input type="datetime-local" name="deadline" id="asg_deadline" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="asg_desc">Instruksi & Deskripsi Tugas</label>
                <textarea name="description" id="asg_desc" rows="3" class="form-input" placeholder="Tuliskan petunjuk pengerjaan dan kriteria penilaian..." required></textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('add-assignment-modal').classList.add('hidden')" class="btn btn-secondary text-xs">Batal</button>
                <button type="submit" class="btn btn-primary text-xs">Terbitkan Tugas</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openSubmitModal(assignmentId, title) {
        document.getElementById('modal-task-title').textContent = title;
        document.getElementById('submit-assignment-form').action = '{{ url("/courses/" . $course["id"] . "/assignments") }}/' + assignmentId + '/submit';
        document.getElementById('submit-modal').classList.remove('hidden');
    }
</script>
@endsection
