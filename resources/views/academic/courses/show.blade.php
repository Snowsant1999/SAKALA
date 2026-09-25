@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', $course['name'])
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ session('user_role') === 'admin' ? url('/admin/courses') : url('/courses') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    <span>{{ $course['name'] }}</span>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Course Header Banner --}}
    <div class="rounded-2xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #1e2788 0%, #2a3dea 60%, #3d5af5 100%);">
        <div class="relative z-10 max-w-3xl">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-white/20 backdrop-blur-sm tracking-wide">{{ $course['code'] }}</span>
                <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-white/10 backdrop-blur-sm">{{ $course['sks'] }} SKS</span>
                <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-white/10 backdrop-blur-sm">Kelas {{ $course['class'] }}</span>
                <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-white/10 backdrop-blur-sm">Semester {{ $course['semester'] }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold mb-3">{{ $course['name'] }}</h1>
            <p class="text-indigo-100 text-sm leading-relaxed mb-4">
                {{ $course['description'] }}
            </p>
            <div class="flex flex-wrap items-center gap-4 text-xs text-indigo-100/90 pt-2 border-t border-white/15">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    <span>Dosen: <strong>{{ $course['lecturer'] }}</strong></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <span>Jadwal: {{ $course['day'] }}, {{ $course['time'] }} ({{ $course['room'] }})</span>
                </div>
            </div>
        </div>
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
    </div>

    {{-- Sub Navigation Tabs --}}
    <div class="flex border-b border-slate-200 gap-6">
        <a href="{{ url('/courses/' . $course['id']) }}" class="pb-3 text-sm font-bold text-navy-600 border-b-2 border-navy-600 flex items-center gap-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
            Overview
        </a>
        <a href="{{ url('/courses/' . $course['id'] . '/materials') }}" class="pb-3 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            Materi Perkuliahan ({{ count($materials) }})
        </a>
        <a href="{{ url('/courses/' . $course['id'] . '/assignments') }}" class="pb-3 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
            Tugas Kuliah ({{ count($assignments) }})
        </a>
    </div>

    {{-- Overview Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Syllabus & Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-navy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                    Capaian Pembelajaran Mata Kuliah (CPMK)
                </h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-navy-50 text-navy-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <span>Mampu menganalisis dan merancang arsitektur sistem berbasis web scalable dengan clean architecture.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-navy-50 text-navy-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <span>Mampu mengimplementasikan RESTful Web Service dengan standar keamanan otorisasi token modern.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-navy-50 text-navy-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <span>Mampu membangun antarmuka web interaktif yang responsif, modular, dan ramah pengguna.</span>
                    </li>
                </ul>
            </div>

            {{-- Recent Materials preview --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800">Materi Terkini</h3>
                    <a href="{{ url('/courses/' . $course['id'] . '/materials') }}" class="text-xs font-semibold text-navy-600 hover:text-navy-700">Lihat Semua &rarr;</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse(array_slice($materials, 0, 2) as $m)
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-xs shrink-0">PDF</div>
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-800">{{ $m['title'] }}</h4>
                                    <div class="text-xs text-slate-400">{{ $m['file_name'] }} • {{ $m['file_size'] }}</div>
                                </div>
                            </div>
                            <button onclick="showToast('Unduhan materi dimulai (mock download)')" class="btn btn-secondary btn-sm">Unduh</button>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-4 text-center">Belum ada materi perkuliahan.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Quick Stats & Lecturer Info --}}
        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-base font-bold text-slate-800 mb-4">Pengampu Kelas</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-navy-100 text-navy-700 font-bold text-base flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($course['lecturer'], 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800">{{ $course['lecturer'] }}</div>
                        <div class="text-xs text-slate-500">Dosen Pengampu Utama</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs text-slate-600 pt-3 border-t border-slate-100">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jurusan:</span>
                        <span class="font-medium text-slate-700">{{ $course['department'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Program Studi:</span>
                        <span class="font-medium text-slate-700">{{ $course['study_program'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Total Mahasiswa:</span>
                        <span class="font-medium text-slate-700">{{ $course['students_count'] }} Mahasiswa</span>
                    </div>
                </div>
            </div>

            {{-- Pending assignments box --}}
            <div class="card p-6 border-l-4 border-l-amber-500">
                <h3 class="text-sm font-bold text-slate-800 mb-2">Tugas Aktif</h3>
                @if(count($assignments) > 0)
                    <p class="text-xs text-slate-600 mb-3">Terdapat {{ count($assignments) }} tugas pada mata kuliah ini.</p>
                    <a href="{{ url('/courses/' . $course['id'] . '/assignments') }}" class="btn btn-primary btn-sm w-full">Buka Tab Tugas</a>
                @else
                    <p class="text-xs text-slate-500">Tidak ada tugas aktif saat ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
