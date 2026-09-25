@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Mata Kuliah')
@section('page-title', 'Mata Kuliah')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header & Search/Filter Toolbar --}}
    <div class="card p-5">
        <form method="GET" action="{{ url('/courses') }}" class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="relative w-full md:w-80">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari mata kuliah atau dosen..." class="form-input pl-10">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select name="semester" class="form-select w-full md:w-48" onchange="this.form.submit()">
                    <option value="all" {{ ($semester ?? '') == 'all' ? 'selected' : '' }}>Semua Semester</option>
                    <option value="5" {{ ($semester ?? '') == '5' ? 'selected' : '' }}>Semester 5 (Ganjil)</option>
                    <option value="4" {{ ($semester ?? '') == '4' ? 'selected' : '' }}>Semester 4 (Genap)</option>
                </select>

                @if(!empty($search) || ($semester && $semester !== 'all'))
                    <a href="{{ url('/courses') }}" class="btn btn-secondary text-xs">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Course Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $c)
            <div class="card flex flex-col justify-between hover:shadow-lg transition-all duration-200 border-t-4 border-t-navy-500 group">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <span class="badge badge-primary font-mono text-xs">{{ $c['code'] }}</span>
                        <span class="badge badge-gray text-xs">{{ $c['sks'] }} SKS</span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-800 group-hover:text-navy-600 transition-colors mb-2 line-clamp-2">
                        <a href="{{ url('/courses/' . $c['id']) }}">{{ $c['name'] }}</a>
                    </h3>

                    <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">
                        {{ $c['description'] }}
                    </p>

                    <div class="space-y-2 pt-3 border-t border-slate-100 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                            <span class="truncate font-medium text-slate-700">{{ $c['lecturer'] }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                <span>{{ $c['day'] }}, {{ $c['time'] }}</span>
                            </div>
                            @if($c['mode'] === 'ONLINE')
                                <span class="badge badge-info text-[10px]">Daring</span>
                            @else
                                <span class="badge badge-gray text-[10px]">{{ $c['room'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium">Kelas {{ $c['class'] }}</span>
                    <a href="{{ url('/courses/' . $c['id']) }}" class="text-navy-600 font-semibold hover:text-navy-700 inline-flex items-center gap-1">
                        Buka Kelas
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full card p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                <div class="text-base font-semibold text-slate-700 mb-1">Tidak ada mata kuliah yang cocok</div>
                <p class="text-sm text-slate-500">Coba ubah kata kunci pencarian atau filter semester Anda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
