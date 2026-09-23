@extends('layouts.app')

@section('title', 'Detail Ruangan - ' . $room['name'])
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ url('/rooms') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    Ruangan Belajar
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col md:flex-row min-h-[600px] animate-fade-in-up">
    
    {{-- Left Panel: Room Detail --}}
    <div class="w-full md:w-1/3 bg-white border-b md:border-b-0 md:border-r border-slate-200 p-8 flex flex-col">
        <div class="mb-2">
            <span class="badge badge-gray text-sm">{{ $room['code'] }}</span>
        </div>
        <h2 class="text-3xl font-bold text-slate-800 mb-6">{{ $room['name'] }}</h2>
        
        <div class="space-y-5 flex-1">
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Lokasi</div>
                <div class="text-slate-800 font-medium">{{ $room['buildingName'] }}</div>
                <div class="text-slate-600 text-sm">{{ $room['floorLabel'] }}</div>
            </div>
            
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kapasitas</div>
                <div class="text-slate-800 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    {{ $room['capacity'] }} orang
                </div>
            </div>

            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tipe Ruangan</div>
                <div class="text-slate-800 font-medium">{{ $room['type'] }}</div>
            </div>

            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status Saat Ini</div>
                <span class="room-status {{ strtolower($room['status']) }} text-sm px-3 py-1.5">
                    @if($room['status'] == 'KOSONG')
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    @elseif($room['status'] == 'DIGUNAKAN')
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    @elseif($room['status'] == 'RESERVED')
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    @endif
                    {{ $room['status'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- Right Panel: Schedule Timeline --}}
    <div class="flex-1 bg-slate-100/50 p-6 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800">Jadwal Hari Ini</h3>
            <span class="text-sm font-medium text-slate-500">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
        </div>

        <div class="space-y-4">
            @forelse($schedules as $schedule)
                @if($schedule['status'] == 'OCCUPIED' || $schedule['status'] == 'RESERVED_SLOT')
                    {{-- Occupied/Reserved Slot --}}
                    <div class="bg-slate-300/40 rounded-xl p-5 border border-slate-300">
                        <div class="text-lg font-bold text-slate-800 mb-3">{{ $schedule['time'] }}</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-slate-500 mb-0.5">Mata Kuliah / Kegiatan</div>
                                <div class="font-semibold text-slate-800">{{ $schedule['course'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 mb-0.5">Dosen / Penanggung Jawab</div>
                                <div class="font-medium text-slate-700">{{ $schedule['lecturer'] ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 mb-0.5">Program Studi & Kelas</div>
                                <div class="font-medium text-slate-700">{{ $schedule['program'] ?? '-' }} {{ $schedule['class'] ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                @elseif($schedule['status'] == 'MAINTENANCE_SLOT')
                    {{-- Maintenance Slot --}}
                    <div class="bg-slate-200/60 rounded-xl p-5 border border-slate-300 flex flex-col items-center justify-center text-center py-10">
                        <svg class="w-10 h-10 text-slate-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.83M11.42 15.17l-3.96 3.96a2.653 2.653 0 01-3.75-3.75l3.96-3.96m5.75 3.75l-5.75-5.75M10.5 4.5l3 3m-3-3l-3 3m3-3v8.25" /></svg>
                        <div class="font-bold text-slate-700">{{ $schedule['time'] }}</div>
                        <div class="text-slate-500">{{ $schedule['course'] }}</div>
                    </div>
                @else
                    {{-- Available Slot --}}
                    <div class="bg-emerald-50/50 rounded-xl p-5 border border-emerald-200 hover:border-emerald-300 hover:shadow-sm transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 cursor-pointer" onclick="showToast('Fitur form reservasi akan dikerjakan pada modul berikutnya.')">
                        <div>
                            <div class="text-lg font-bold text-slate-800">{{ $schedule['time'] }}</div>
                            <div class="text-sm font-medium text-emerald-600 mt-1">Kosong (Tersedia)</div>
                        </div>
                        <button class="btn btn-success">Ajukan Reservasi</button>
                    </div>
                @endif
            @empty
                <div class="p-8 text-center text-slate-500 bg-white rounded-xl border border-slate-200">
                    Tidak ada data jadwal untuk ruangan ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
