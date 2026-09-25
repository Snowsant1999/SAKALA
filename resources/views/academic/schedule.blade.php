@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Jadwal Perkuliahan')
@section('page-title', 'Jadwal Perkuliahan')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Day Filter Tabs --}}
    <div class="card p-3">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                <a href="{{ url('/schedule?day=' . $day) }}" class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all shrink-0 {{ ($selectedDay === $day) ? 'bg-navy-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $day }}
                    <span class="ml-1.5 text-xs opacity-75 font-normal">({{ count($schedules[$day] ?? []) }})</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Schedule Cards for Selected Day --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-navy-600"></span>
                Jadwal Hari {{ $selectedDay }}
            </h2>
            <span class="text-xs text-slate-500 font-medium">Semester 5 (Ganjil 2026/2027)</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($schedules[$selectedDay] ?? [] as $s)
                <div class="card p-6 border-t-4 hover:shadow-lg transition-all duration-200" style="border-top-color: {{ $s['mode'] === 'ONLINE' ? '#8b5cf6' : '#3d5af5' }};">
                    <div class="flex items-center justify-between mb-3">
                        <span class="badge badge-primary font-mono text-xs">{{ $s['code'] }}</span>
                        @if($s['mode'] === 'ONLINE')
                            <span class="badge bg-purple-50 text-purple-700 text-xs font-semibold">🌐 Daring (Online)</span>
                        @else
                            <span class="badge badge-success text-xs font-semibold">📍 Tatap Muka</span>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-slate-800 mb-1 line-clamp-1">{{ $s['course'] }}</h3>
                    <div class="text-xs text-slate-500 mb-4">{{ $s['lecturer'] }}</div>

                    <div class="space-y-2 pt-3 border-t border-slate-100 text-xs text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Waktu:</span>
                            <span class="font-bold text-slate-800">{{ $s['time'] }} WITA</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Lokasi / Ruangan:</span>
                            <span class="font-semibold text-slate-700">{{ $s['room'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Kelas:</span>
                            <span class="font-medium text-slate-700">{{ $s['class'] }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full card p-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    <div class="text-base font-semibold text-slate-700 mb-1">Tidak ada perkuliahan pada hari {{ $selectedDay }}</div>
                    <p class="text-sm text-slate-500">Gunakan waktu ini untuk belajar mandiri atau diskusi kelompok.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
