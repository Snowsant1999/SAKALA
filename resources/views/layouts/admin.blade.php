<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SAKALA Admin — Sistem Akademik, Kampus Aman, Layanan Aspirasi">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — SAKALA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Admin Sidebar --}}
    <aside class="sidebar" id="sidebar" style="background: #080e1f;">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-icon" style="background: linear-gradient(135deg, #6366f1, #4338ca);">A</div>
            <span class="logo-text sidebar-text">SAKALA <sup class="text-[10px] text-indigo-400 font-normal">Admin</sup></span>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            {{-- Dashboard --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Overview</div>
                <a href="{{ url('/admin/dashboard') }}" class="sidebar-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z" /></svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </div>

            {{-- Akademik --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Akademik</div>
                <a href="{{ url('/admin/students') }}" class="sidebar-item {{ request()->is('admin/students*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10v6M2 10l10-5 10 5-10 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 12v5c0 1.657 2.686 3 6 3s6-1.343 6-3v-5" /></svg>
                    <span class="sidebar-text">Mahasiswa</span>
                </a>
                <a href="{{ url('/admin/lecturers') }}" class="sidebar-item {{ request()->is('admin/lecturers*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    <span class="sidebar-text">Dosen</span>
                </a>
                <a href="{{ url('/admin/departments') }}" class="sidebar-item {{ request()->is('admin/departments*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                    <span class="sidebar-text">Jurusan</span>
                </a>
                <a href="{{ url('/admin/study-programs') }}" class="sidebar-item {{ request()->is('admin/study-programs*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072" /></svg>
                    <span class="sidebar-text">Program Studi</span>
                </a>
                <a href="{{ url('/admin/courses') }}" class="sidebar-item {{ request()->is('admin/courses*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                    <span class="sidebar-text">Mata Kuliah</span>
                </a>
                <a href="{{ url('/admin/classes') }}" class="sidebar-item {{ request()->is('admin/classes*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                    <span class="sidebar-text">Kelas</span>
                </a>
                <a href="{{ url('/admin/schedules') }}" class="sidebar-item {{ request()->is('admin/schedules*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    <span class="sidebar-text">Jadwal</span>
                </a>
            </div>

            {{-- Ruangan --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Ruangan</div>
                <a href="{{ url('/admin/buildings') }}" class="sidebar-item {{ request()->is('admin/buildings*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" /></svg>
                    <span class="sidebar-text">Gedung</span>
                </a>
                <a href="{{ url('/admin/rooms') }}" class="sidebar-item {{ request()->is('admin/rooms*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" /></svg>
                    <span class="sidebar-text">Ruangan</span>
                </a>
                <a href="{{ url('/admin/reservations') }}" class="sidebar-item {{ request()->is('admin/reservations*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                    <span class="sidebar-text">Reservasi</span>
                </a>
            </div>

            {{-- Kampus Aman --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Kampus Aman</div>
                <a href="{{ url('/admin/reports') }}" class="sidebar-item {{ request()->is('admin/reports*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286ZM12 15h.008v.008H12V15Z" /></svg>
                    <span class="sidebar-text">Laporan Keamanan</span>
                </a>
            </div>

            {{-- Layanan Aspirasi --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Layanan Aspirasi</div>
                <a href="{{ url('/admin/aspirations') }}" class="sidebar-item {{ request()->is('admin/aspirations*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" /></svg>
                    <span class="sidebar-text">Aspirasi & Sarpras</span>
                </a>
            </div>

            {{-- System --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Sistem</div>
                <a href="{{ url('/admin/users') }}" class="sidebar-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    <span class="sidebar-text">Users</span>
                </a>
            </div>
        </nav>

        {{-- User Info --}}
        <div class="sidebar-user">
            <a href="{{ url('/profile') }}" class="avatar hover:ring-2 hover:ring-indigo-400 transition-all flex items-center justify-center" style="background: #4338ca;" title="Buka Profil">{{ strtoupper(substr(session('user_name', 'A'), 0, 1)) }}</a>
            <a href="{{ url('/profile') }}" class="user-info sidebar-text hover:text-white transition-colors" title="Buka Profil">
                <div class="user-name truncate">{{ session('user_name', 'Admin') }}</div>
                <div class="user-role">Administrator</div>
            </a>
            <form action="{{ url('/logout') }}" method="POST" class="sidebar-text">
                @csrf
                <button type="submit" class="text-slate-500 hover:text-red-400 transition-colors" title="Logout">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile Overlay --}}
    <div class="fixed inset-0 bg-black/50 z-30 hidden" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    {{-- Main Content --}}
    <div class="main-content" id="main-content">
        {{-- Top Navbar --}}
        <header class="top-navbar">
            <div class="flex items-center gap-3">
                <button class="lg:hidden text-slate-600 hover:text-slate-800" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
                <h1 class="page-title">@yield('page-title', 'Admin Dashboard')</h1>
            </div>
            <div class="nav-actions flex items-center gap-3">
                @yield('nav-actions')

                {{-- Notification Bell & Dropdown --}}
                <div class="relative" id="notif-dropdown-wrapper">
                    <button type="button" onclick="toggleNotificationDropdown()" class="relative p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors focus:outline-none" title="Notifikasi">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span id="notif-badge" class="hidden absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white animate-pulse"></span>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="notif-dropdown-menu" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 py-3 z-50">
                        <div class="px-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-800">Notifikasi</span>
                                <span id="notif-unread-count" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-600">0 Baru</span>
                            </div>
                            <button type="button" onclick="markAllNotifsRead()" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold">Tandai dibaca</button>
                        </div>

                        <div id="notif-dropdown-items" class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                            <div class="p-6 text-center text-xs text-slate-400">Memuat notifikasi...</div>
                        </div>

                        <div class="pt-2.5 px-4 border-t border-slate-100 text-center">
                            <a href="{{ url('/notifications') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 block py-1">Lihat Semua Notifikasi &rarr;</a>
                        </div>
                    </div>
                </div>

                {{-- Admin Profile Quick Link --}}
                <a href="{{ url('/profile') }}" class="flex items-center gap-2 pl-2 py-1 pr-2 rounded-xl hover:bg-slate-100 transition-colors" title="Profil Akun">
                    <div class="w-8 h-8 rounded-full bg-indigo-700 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        {{ strtoupper(substr(session('user_name', 'A'), 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="text-xs font-bold text-slate-800 leading-none truncate max-w-[120px]">{{ session('user_name', 'Admin') }}</div>
                        <div class="text-[10px] text-indigo-600 font-semibold leading-none mt-1">Admin</div>
                    </div>
                </a>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="content-area">
            @yield('content')
        </main>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container" id="toast-container"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (window.innerWidth <= 1024) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('hidden');
            } else {
                sidebar.classList.toggle('collapsed');
                document.querySelectorAll('.sidebar-text').forEach(el => {
                    el.classList.toggle('hidden');
                });
            }
        }

        // Notification Dropdown Toggle & Fetch
        function toggleNotificationDropdown() {
            const menu = document.getElementById('notif-dropdown-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                loadNotifications();
            } else {
                menu.classList.add('hidden');
            }
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notif-dropdown-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const menu = document.getElementById('notif-dropdown-menu');
                if (menu) menu.classList.add('hidden');
            }
        });

        // Load notifications from API
        function loadNotifications() {
            fetch('{{ url("/notifications") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;
                
                const badge = document.getElementById('notif-badge');
                const unreadLabel = document.getElementById('notif-unread-count');
                const container = document.getElementById('notif-dropdown-items');

                if (data.unread_count > 0) {
                    badge.classList.remove('hidden');
                    unreadLabel.classList.remove('hidden');
                    unreadLabel.innerText = `${data.unread_count} Baru`;
                } else {
                    badge.classList.add('hidden');
                    unreadLabel.classList.add('hidden');
                }

                if (data.notifications.length === 0) {
                    container.innerHTML = '<div class="p-6 text-center text-xs text-slate-400">Tidak ada notifikasi</div>';
                    return;
                }

                let html = '';
                data.notifications.forEach(n => {
                    const unreadClass = !n.is_read ? 'bg-indigo-50/50 font-medium' : '';
                    const link = n.link ? `href="${n.link}"` : 'href="#"';
                    html += `
                        <a ${link} class="block p-3.5 hover:bg-slate-50 transition-colors ${unreadClass}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="text-xs font-bold text-slate-800">${escapeHtml(n.title)}</div>
                                <span class="text-[10px] text-slate-400 shrink-0">${n.time_ago}</span>
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-2 mt-1">${escapeHtml(n.message)}</p>
                        </a>
                    `;
                });
                container.innerHTML = html;
            })
            .catch(err => console.error('Error loading notifications:', err));
        }

        // Mark All Read
        function markAllNotifsRead() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('{{ url("/notifications/read-all") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('notif-badge').classList.add('hidden');
                    document.getElementById('notif-unread-count').classList.add('hidden');
                    loadNotifications();
                }
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initial check for unread badge on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetch('{{ url("/notifications") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.unread_count > 0) {
                    document.getElementById('notif-badge')?.classList.remove('hidden');
                    const unreadLabel = document.getElementById('notif-unread-count');
                    if (unreadLabel) {
                        unreadLabel.classList.remove('hidden');
                        unreadLabel.innerText = `${data.unread_count} Baru`;
                    }
                }
            })
            .catch(() => {});
        });

        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white">&times;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>

    @yield('scripts')
</body>
</html>
