@extends('layouts.admin')

@section('title', 'Master Data Jadwal')
@section('page-title', 'Jadwal Perkuliahan')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold text-slate-800">Jadwal Perkuliahan</h2>
            <p class="text-xs text-slate-500 mt-1">Semester Ganjil 2026/2027 — Politeknik Negeri Samarinda</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="form-input text-xs" id="dayFilter">
                <option value="">Semua Hari</option>
                <option>Senin</option>
                <option>Selasa</option>
                <option>Rabu</option>
                <option>Kamis</option>
                <option>Jumat</option>
            </select>
            <select class="form-input text-xs" id="programFilter">
                <option value="">Semua Prodi</option>
                <option>TIM</option>
                <option>TRK</option>
            </select>
        </div>
    </div>

    {{-- Schedule Timeline --}}
    @php
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $dayColors = [
            'Senin' => ['bg' => '#eef2ff', 'border' => '#6366f1', 'text' => '#4338ca'],
            'Selasa' => ['bg' => '#ecfdf5', 'border' => '#10b981', 'text' => '#059669'],
            'Rabu' => ['bg' => '#fef3c7', 'border' => '#f59e0b', 'text' => '#d97706'],
            'Kamis' => ['bg' => '#fce7f3', 'border' => '#ec4899', 'text' => '#db2777'],
            'Jumat' => ['bg' => '#f0f9ff', 'border' => '#0ea5e9', 'text' => '#0369a1'],
        ];
    @endphp

    @foreach($days as $day)
        @php
            $daySchedules = $schedules[$day] ?? [];
        @endphp

        @if(count($daySchedules) > 0)
            <div class="card overflow-hidden">
                {{-- Day Header --}}
                <div class="px-5 py-3 flex items-center gap-3" style="background: {{ $dayColors[$day]['bg'] }}; border-left: 4px solid {{ $dayColors[$day]['border'] }};">
                    <svg class="w-5 h-5" style="color: {{ $dayColors[$day]['text'] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    <h3 class="text-sm font-bold" style="color: {{ $dayColors[$day]['text'] }};">{{ $day }}</h3>
                    <span class="badge text-[10px]" style="background: {{ $dayColors[$day]['border'] }}; color: white;">{{ count($daySchedules) }} jadwal</span>
                </div>

                {{-- Schedule Items --}}
                <div class="divide-y divide-slate-100">
                    @foreach($daySchedules as $s)
                        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-slate-50 transition-colors">
                            {{-- Time --}}
                            <div class="flex items-center gap-2 sm:w-36 shrink-0">
                                <div class="w-2.5 h-2.5 rounded-full" style="background: {{ $dayColors[$day]['border'] }};"></div>
                                <span class="text-xs font-bold font-mono" style="color: {{ $dayColors[$day]['text'] }};">{{ $s['time'] }}</span>
                            </div>

                            {{-- Course Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-slate-800 truncate">{{ $s['course'] }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $s['code'] ?? '' }}</div>
                            </div>

                            {{-- Lecturer --}}
                            <div class="flex items-center gap-2 sm:w-48 shrink-0">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white" style="background: {{ $dayColors[$day]['border'] }};">
                                    {{ strtoupper(substr($s['lecturer'], 0, 1)) }}
                                </div>
                                <span class="text-xs text-slate-600 truncate">{{ $s['lecturer'] }}</span>
                            </div>

                            {{-- Room --}}
                            <div class="sm:w-24 shrink-0">
                                <span class="badge text-[10px]" style="background: {{ $dayColors[$day]['bg'] }}; color: {{ $dayColors[$day]['text'] }};">
                                    {{ $s['room'] }}
                                </span>
                            </div>

                            {{-- Class --}}
                            <div class="sm:w-16 shrink-0 text-xs font-semibold text-slate-600">
                                {{ $s['class'] ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection
