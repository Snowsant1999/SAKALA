@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')

@section('nav-actions')
    <span class="text-sm text-slate-500 mr-2">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
@endsection

@section('content')
<div class="mb-6 animate-fade-in-up">
    <h2 class="text-2xl font-bold text-slate-800">Halo, {{ session('user_name', 'Mahasiswa') }}! 👋</h2>
    <p class="text-slate-500 mt-1">Berikut adalah ringkasan aktivitas akademik Anda hari ini.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column (Wider) --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Jadwal Hari Ini --}}
        <div class="card animate-fade-in-up animate-delay-1">
            <div class="card-header">
                <h3 class="card-title">Jadwal Kuliah Hari Ini</h3>
                <a href="{{ url('/schedule') }}" class="text-sm text-navy-600 hover:text-navy-800 font-medium">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($todaySchedule as $schedule)
                <div class="flex items-center gap-4 p-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <div class="w-20 text-center shrink-0">
                        <div class="text-sm font-bold text-slate-800">{{ explode(' - ', $schedule['time'])[0] }}</div>
                        <div class="text-xs text-slate-500">{{ explode(' - ', $schedule['time'])[1] }}</div>
                    </div>
                    <div class="w-1 bg-navy-200 h-10 rounded-full shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-base font-semibold text-slate-800 truncate">{{ $schedule['course'] }}</h4>
                        <div class="text-sm text-slate-500 flex items-center gap-3 mt-0.5">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                {{ $schedule['room'] }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a23.838 23.838 0 0 0-1.012 5.434c0 .054.006.108.011.162a48.68 48.68 0 0 1 7.233 3.891A48.68 48.68 0 0 1 12 20.904a48.68 48.68 0 0 1 4.725-3.87 48.68 48.68 0 0 1 7.233-3.891c.005-.054.011-.108.011-.162a23.838 23.838 0 0 0-1.012-5.434m-15.482 0A23.899 23.899 0 0 1 12 2.25a23.899 23.899 0 0 1 7.74 7.897" /></svg>
                                {{ $schedule['class'] }}
                            </span>
                        </div>
                    </div>
                    <div>
                        @if($schedule['mode'] == 'ONLINE')
                            <span class="badge badge-info">Online</span>
                        @else
                            <span class="badge badge-primary">Onsite</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-500">
                    Tidak ada jadwal kuliah hari ini.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Mata Kuliah Aktif --}}
        <div class="card animate-fade-in-up animate-delay-2">
            <div class="card-header">
                <h3 class="card-title">Mata Kuliah Aktif</h3>
                <a href="{{ url('/courses') }}" class="text-sm text-navy-600 hover:text-navy-800 font-medium">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($activeCourses as $course)
                    <div class="border border-slate-200 rounded-lg p-4 hover:border-navy-300 hover:shadow-sm transition-all cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <span class="badge badge-gray">{{ $course['code'] }}</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $course['class'] }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-800 mb-1 line-clamp-1" title="{{ $course['name'] }}">{{ $course['name'] }}</h4>
                        <p class="text-xs text-slate-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                            {{ $course['lecturer'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- Right Column (Narrower) --}}
    <div class="space-y-6">
        
        {{-- Tugas Pending --}}
        <div class="card animate-fade-in-up animate-delay-3">
            <div class="card-header">
                <h3 class="card-title">Tugas Belum Selesai</h3>
                <span class="badge badge-danger">{{ count($pendingAssignments) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-slate-100">
                    @forelse($pendingAssignments as $task)
                    <div class="p-4 hover:bg-slate-50 transition-colors cursor-pointer">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h4 class="font-semibold text-sm text-slate-800 line-clamp-2">{{ $task['title'] }}</h4>
                            @if($task['urgency'] == 'high')
                                <span class="w-2 h-2 rounded-full bg-red-500 shrink-0 mt-1.5" title="Mendesak"></span>
                            @elseif($task['urgency'] == 'medium')
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0 mt-1.5" title="Sedang"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 mt-1.5" title="Rendah"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mb-2 truncate">{{ $task['course'] }}</p>
                        <div class="flex items-center gap-1.5 text-xs font-medium {{ $task['urgency'] == 'high' ? 'text-red-600' : 'text-slate-600' }}">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            Tenggat: {{ \Carbon\Carbon::parse($task['deadline'])->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-sm text-slate-500">
                        Tidak ada tugas yang tertunda. Hebat! 🎉
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Status Reservasi Terbaru --}}
        <div class="card animate-fade-in-up animate-delay-4">
            <div class="card-header">
                <h3 class="card-title">Reservasi Terbaru</h3>
                <a href="{{ url('/reservations') }}" class="text-sm text-navy-600 hover:text-navy-800 font-medium">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-slate-100">
                    @forelse($recentReservations as $rsv)
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-semibold text-sm text-slate-800">{{ $rsv['room'] }}</h4>
                            <span class="badge badge-warning text-[10px]">{{ $rsv['status'] }}</span>
                        </div>
                        <p class="text-xs text-slate-500 flex items-center gap-1 mb-1">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            {{ \Carbon\Carbon::parse($rsv['date'])->format('d/m/Y') }} • {{ $rsv['time'] }}
                        </p>
                    </div>
                    @empty
                    <div class="p-4 text-center text-sm text-slate-500">
                        Belum ada pengajuan reservasi.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
