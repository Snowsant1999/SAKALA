<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SAKALA — Sistem Akademik, Kampus Aman, Layanan Aspirasi">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SAKALA') — Sistem Akademik Kampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-icon">S</div>
            <span class="logo-text sidebar-text">SAKALA</span>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            {{-- Main --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Menu Utama</div>
                <a href="{{ url('/dashboard') }}" class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z" /></svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </div>

            {{-- Akademik --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Akademik</div>
                <a href="{{ url('/courses') }}" class="sidebar-item {{ request()->is('courses*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                    <span class="sidebar-text">Mata Kuliah</span>
                </a>
                <a href="{{ url('/schedule') }}" class="sidebar-item {{ request()->is('schedule*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    <span class="sidebar-text">Jadwal</span>
                </a>
            </div>

            {{-- Ruangan --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Ruangan</div>
                <a href="{{ url('/rooms') }}" class="sidebar-item {{ request()->is('rooms*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" /></svg>
                    <span class="sidebar-text">Ruangan Belajar</span>
                </a>
                <a href="{{ url('/reservations') }}" class="sidebar-item {{ request()->is('reservations*') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                    <span class="sidebar-text">Reservasi Saya</span>
                </a>
            </div>

            {{-- Kampus Aman --}}
            <div class="sidebar-section">
                <div class="sidebar-section-title sidebar-text">Kampus Aman</div>
                <a href="{{ url('/reports/create') }}" class="sidebar-item {{ request()->is('reports/create') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286ZM12 15h.008v.008H12V15Z" /></svg>
                    <span class="sidebar-text">Buat Laporan</span>
                </a>
                <a href="{{ url('/reports') }}" class="sidebar-item {{ request()->is('reports') ? 'active' : '' }}">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                    <span class="sidebar-text">Laporan Saya</span>
                </a>
            </div>
        </nav>

        {{-- User Info --}}
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}</div>
            <div class="user-info sidebar-text">
                <div class="user-name">{{ session('user_name', 'User') }}</div>
                <div class="user-role">{{ ucfirst(session('user_role', 'guest')) }}</div>
            </div>
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
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="nav-actions">
                {{-- Breadcrumb or extra actions --}}
                @yield('nav-actions')
                <div class="flex items-center gap-2 ml-2">
                    <span class="badge badge-primary">{{ ucfirst(session('user_role', 'guest')) }}</span>
                </div>
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
        // Sidebar Toggle
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

        // Toast Function
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
