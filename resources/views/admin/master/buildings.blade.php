@extends('layouts.admin')

@section('title', 'Master Data Gedung')
@section('page-title', 'Data Gedung')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold text-slate-800">Gedung Kampus</h2>
            <p class="text-xs text-slate-500 mt-1">Manajemen gedung dan fasilitas Politeknik Negeri Samarinda</p>
        </div>
        <button onclick="showToast('Fitur tambah gedung (Master Data)', 'info')" class="btn btn-primary text-xs shrink-0">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Gedung
        </button>
    </div>

    {{-- Buildings Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($buildings as $b)
            <div class="card p-0 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                {{-- Building Header --}}
                <div class="p-5 relative" style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
                    <div class="absolute top-3 right-3">
                        <span class="badge text-[10px] font-bold" style="background: rgba(255,255,255,0.15); color: #e0e7ff;">{{ $b['code'] }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <svg class="w-6 h-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">{{ $b['name'] }}</h3>
                    <p class="text-xs text-indigo-300 mt-1">{{ $b['location'] }}</p>
                </div>

                {{-- Building Stats --}}
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Lantai</div>
                        <div class="text-xl font-bold text-navy-700">{{ $b['floors_count'] }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Ruangan</div>
                        <div class="text-xl font-bold text-indigo-600">{{ $b['rooms_count'] }}</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-5 pb-4 flex justify-end gap-2">
                    <button onclick="showToast('Detail {{ $b['name'] }}', 'info')" class="text-xs font-semibold text-navy-600 hover:text-navy-800">Detail</button>
                    <button onclick="showToast('Edit {{ $b['name'] }}', 'info')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Edit</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
