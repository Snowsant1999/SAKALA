@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md mx-auto">
    {{-- Logo & Branding --}}
    <div class="text-center mb-8 animate-fade-in-up">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 shadow-lg shadow-navy-600/30" style="background: linear-gradient(135deg, #3d5af5, #222fd6);">
            <span class="text-white text-2xl font-bold">S</span>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">SAKALA</h1>
        <p class="text-slate-300 text-sm">Sistem Akademik, Kampus Aman, Layanan Aspirasi</p>
    </div>

    {{-- Login Card --}}
    <div class="login-card w-full animate-fade-in-up animate-delay-2">
        <h2 class="text-xl font-bold text-slate-800 mb-1">Selamat Datang</h2>
        <p class="text-sm text-slate-500 mb-6">Masuk ke akun Anda untuk melanjutkan</p>

        {{-- Error Message --}}
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Login Form --}}
        <form action="{{ url('/login') }}" method="POST" id="login-form">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="Masukkan email Anda" required value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="Masukkan password" required value="password">
            </div>
            <button type="submit" class="btn btn-primary w-full mt-2">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                Masuk
            </button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-xs text-slate-400 font-medium">Demo Accounts</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        {{-- Demo Account Cards --}}
        <div class="space-y-2">
            <div class="demo-account-card" onclick="fillAccount('student@sakala.test', 'Andi Pratama', 'student')">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-semibold text-sm shrink-0">AP</div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-slate-800">Andi Pratama</div>
                    <div class="text-xs text-slate-500">student@sakala.test</div>
                </div>
                <span class="badge badge-success">Mahasiswa</span>
            </div>

            <div class="demo-account-card" onclick="fillAccount('lecturer@sakala.test', 'Dr. Budi Santoso', 'lecturer')">
                <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-700 font-semibold text-sm shrink-0">BS</div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-slate-800">Dr. Budi Santoso</div>
                    <div class="text-xs text-slate-500">lecturer@sakala.test</div>
                </div>
                <span class="badge badge-info">Dosen</span>
            </div>

            <div class="demo-account-card" onclick="fillAccount('admin@sakala.test', 'Admin SAKALA', 'admin')">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm shrink-0">AS</div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-slate-800">Admin SAKALA</div>
                    <div class="text-xs text-slate-500">admin@sakala.test</div>
                </div>
                <span class="badge badge-primary">Admin</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <p class="text-center text-slate-500 text-xs mt-6 animate-fade-in-up animate-delay-4">
        &copy; {{ date('Y') }} SAKALA — Politeknik Negeri Samarinda
    </p>
</div>
@endsection

@section('scripts')
<script>
    function fillAccount(email, name, role) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';

        // Visual feedback
        document.querySelectorAll('.demo-account-card').forEach(card => card.classList.remove('selected'));
        event.currentTarget.classList.add('selected');
    }
</script>
@endsection
