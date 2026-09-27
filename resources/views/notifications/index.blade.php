@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Pusat Notifikasi')
@section('page-title', 'Notifikasi Saya')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto animate-fade-in-up">

    {{-- Header Actions Toolbar --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Semua Pemberitahuan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status pengajuan reservasi, laporan, aspirasi, dan informasi akademik Anda.</p>
        </div>

        @if($unreadCount > 0)
            <form action="{{ url('/notifications/read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary text-xs font-semibold">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    {{-- Notification List --}}
    <div class="space-y-3">
        @forelse($notifications as $notif)
            <div class="card p-4 transition-all duration-200 hover:shadow-md flex items-start gap-4 {{ !$notif['is_read'] ? 'bg-indigo-50/40 border-l-4 border-indigo-500' : 'border border-slate-200/80' }}">
                
                {{-- Type Icon --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ !$notif['is_read'] ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500' }}">
                    @if(str_contains($notif['type'], 'reservation') || str_contains($notif['type'], 'room'))
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                    @elseif(str_contains($notif['type'], 'report') || str_contains($notif['type'], 'aman'))
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286ZM12 15h.008v.008H12V15Z" /></svg>
                    @elseif(str_contains($notif['type'], 'aspiration'))
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" /></svg>
                    @elseif(str_contains($notif['type'], 'assignment') || str_contains($notif['type'], 'course'))
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                    @else
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-slate-800 {{ !$notif['is_read'] ? 'text-indigo-950' : '' }}">{{ $notif['title'] }}</h3>
                        <span class="text-[11px] text-slate-400 shrink-0">{{ $notif['time_ago'] }}</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $notif['message'] }}</p>

                    <div class="pt-2 flex items-center gap-3">
                        @if($notif['link'])
                            <a href="{{ url($notif['link']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                                Lihat Detail
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </a>
                        @endif

                        @if(!$notif['is_read'])
                            <form action="{{ url('/notifications/' . $notif['id'] . '/read') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 underline">Tandai Dibaca</button>
                            </form>
                        @endif
                    </div>
                </div>

                @if(!$notif['is_read'])
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 shrink-0 mt-1" title="Belum dibaca"></span>
                @endif
            </div>
        @empty
            <div class="card p-12 text-center text-slate-400 space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                </div>
                <div class="text-sm font-semibold text-slate-600">Tidak ada notifikasi</div>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Saat ini belum ada pemberitahuan baru terkait aktivitas akademik atau pengajuan Anda.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
