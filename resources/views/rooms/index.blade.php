@extends('layouts.app')

@section('title', 'Ruangan Belajar')
@section('page-title', 'Ruangan Belajar')

@section('content')
<div class="mb-6 animate-fade-in-up">
    <p class="text-slate-500">Cari dan ajukan reservasi ruangan untuk kegiatan akademik.</p>
</div>

{{-- Top Controls (Search & Filter) --}}
<div class="flex flex-col md:flex-row gap-4 mb-6 animate-fade-in-up animate-delay-1">
    {{-- Search Bar --}}
    <form action="{{ url('/rooms') }}" method="GET" class="w-full md:w-80 relative">
        <input type="hidden" name="building_id" value="{{ $selectedBuildingId }}">
        <input type="hidden" name="floor_id" value="{{ $selectedFloorId }}">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
        </div>
        <input type="text" name="q" class="form-input pl-10" placeholder="Cari kelas..." value="{{ $searchQuery }}">
    </form>

    {{-- Building Selector --}}
    <form action="{{ url('/rooms') }}" method="GET" class="w-full md:w-64" id="building-form">
        <input type="hidden" name="q" value="{{ $searchQuery }}">
        <select name="building_id" class="form-select" onchange="document.getElementById('building-form').submit()">
            @foreach($buildings as $building)
                <option value="{{ $building['id'] }}" {{ $selectedBuildingId == $building['id'] ? 'selected' : '' }}>
                    {{ $building['name'] }}
                </option>
            @endforeach
        </select>
    </form>
</div>

{{-- Main Room Container --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm animate-fade-in-up animate-delay-2 overflow-hidden flex flex-col md:flex-row min-h-[500px]">
    
    {{-- Left/Top: Floor Tabs --}}
    <div class="w-full md:w-32 bg-slate-50 border-b md:border-b-0 md:border-r border-slate-200 flex md:flex-col p-2 gap-1 overflow-x-auto">
        @forelse($floors as $floor)
        <a href="{{ url('/rooms?building_id='.$selectedBuildingId.'&floor_id='.$floor['id'].'&q='.$searchQuery) }}" 
           class="px-4 py-3 text-sm font-semibold rounded-lg text-center shrink-0 transition-colors
                  {{ $selectedFloorId == $floor['id'] ? 'bg-white text-navy-700 shadow-sm border border-slate-200/60' : 'text-slate-500 hover:bg-slate-200/50 hover:text-slate-700' }}">
            {{ $floor['label'] }}
        </a>
        @empty
        <div class="p-4 text-center text-sm text-slate-500">
            Tidak ada data lantai.
        </div>
        @endforelse
    </div>

    {{-- Right/Bottom: Room Grid --}}
    <div class="flex-1 p-6 bg-slate-100/50">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($rooms as $room)
            <a href="{{ url('/rooms/'.$room['id']) }}" class="room-card group">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-slate-400">{{ $room['code'] }}</span>
                </div>
                <h3 class="room-name group-hover:text-navy-700">{{ $room['name'] }}</h3>
                <p class="room-capacity flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    Kapasitas: {{ $room['capacity'] }} orang
                </p>
                <div class="mt-4 pt-4 border-t border-slate-100 flex justify-center">
                    <span class="room-status {{ strtolower($room['status']) }}">
                        @if($room['status'] == 'KOSONG')
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @elseif($room['status'] == 'DIGUNAKAN')
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        @elseif($room['status'] == 'RESERVED')
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        @else
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        @endif
                        {{ $room['status'] }}
                    </span>
                </div>
            </a>
            @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 text-slate-400">
                <svg class="w-16 h-16 mb-4 opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                <p class="text-lg font-medium">Ruangan tidak ditemukan</p>
                <p class="text-sm mt-1">Coba gunakan kata kunci atau lantai lain.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
