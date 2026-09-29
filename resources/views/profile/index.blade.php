@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Profil Pengguna')
@section('page-title', 'Profil & Pengaturan Akun')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto animate-fade-in-up">

    {{-- Feedback Alerts --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm shadow-sm space-y-1">
            <div class="font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                Terjadi kesalahan input:
            </div>
            <ul class="list-disc list-inside text-xs text-rose-700 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- User Header Banner Card --}}
    <div class="card p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl relative overflow-hidden shadow-xl border border-indigo-900/40">
        {{-- Background decorative shapes --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -top-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            {{-- Big Avatar --}}
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-4xl font-extrabold text-white shadow-lg border-2 border-indigo-300/30 shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            {{-- Info --}}
            <div class="flex-1 text-center sm:text-left space-y-2">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                    <h2 class="text-2xl font-bold text-white tracking-tight">{{ $user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider {{ $user->role === 'admin' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($user->role === 'dosen' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30') }}">
                        {{ $user->role }}
                    </span>
                </div>

                <div class="text-indigo-200/80 text-sm flex flex-wrap items-center justify-center sm:justify-start gap-y-1 gap-x-4">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                        {{ $user->email }}
                    </span>
                    @if($user->nim_nip)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" /></svg>
                            {{ $user->role === 'mahasiswa' ? 'NIM: ' : 'NIP: ' }}{{ $user->nim_nip }}
                        </span>
                    @endif
                    @if($user->studyProgram)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>
                            {{ $user->studyProgram->name }} ({{ $user->department->name ?? 'Fakultas' }})
                        </span>
                    @endif
                </div>

                <div class="pt-2 text-xs text-indigo-300/60">
                    Terdaftar sejak: {{ $user->created_at ? $user->created_at->format('d F Y') : '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Stats Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="card p-4 flex items-center gap-3.5 hover:shadow-md transition-shadow">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" /></svg>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ $stats['reservations_count'] }}</div>
                <div class="text-xs text-slate-500">Reservasi Ruang</div>
            </div>
        </div>

        <div class="card p-4 flex items-center gap-3.5 hover:shadow-md transition-shadow">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286ZM12 15h.008v.008H12V15Z" /></svg>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ $stats['reports_count'] }}</div>
                <div class="text-xs text-slate-500">Laporan Keamanan</div>
            </div>
        </div>

        <div class="card p-4 flex items-center gap-3.5 hover:shadow-md transition-shadow">
            <div class="w-11 h-11 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" /></svg>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">{{ $stats['aspirations_count'] }}</div>
                <div class="text-xs text-slate-500">Aspirasi Fasilitas</div>
            </div>
        </div>

        <div class="card p-4 flex items-center gap-3.5 hover:shadow-md transition-shadow">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>
            </div>
            <div>
                <div class="text-xl font-bold text-slate-800">
                    {{ $user->role === 'dosen' ? ($stats['classes_count'] ?? 0) : ($user->role === 'mahasiswa' ? ($stats['submissions_count'] ?? 0) : 'Aktif') }}
                </div>
                <div class="text-xs text-slate-500">
                    {{ $user->role === 'dosen' ? 'Kelas Diampu' : ($user->role === 'mahasiswa' ? 'Tugas Terkumpul' : 'Status Akun') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid: Profile Form & Password Form --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left Form: Update Profile Info (7 Cols) --}}
        <div class="lg:col-span-7 card p-6 space-y-5">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Informasi Pribadi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui data profil dan kontak Anda yang terdaftar.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">ID: #{{ $user->id }}</span>
            </div>

            <form action="{{ url('/profile') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input" placeholder="Masukkan nama lengkap">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Alamat Email (Akun)</label>
                        <input type="email" value="{{ $user->email }}" disabled class="input bg-slate-50 text-slate-500 cursor-not-allowed" title="Email akun tidak dapat diubah secara mandiri">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">{{ $user->role === 'mahasiswa' ? 'Nomor Induk Mahasiswa (NIM)' : 'Nomor Induk Pegawai (NIP)' }}</label>
                        <input type="text" value="{{ $user->nim_nip ?? '-' }}" disabled class="input bg-slate-50 text-slate-500 cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input" placeholder="Contoh: 081234567890">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Peran Akun (Role)</label>
                        <input type="text" value="{{ ucfirst($user->role) }}" disabled class="input bg-slate-50 text-slate-500 capitalize cursor-not-allowed">
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="btn btn-primary text-xs font-semibold px-5">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Form: Change Password (5 Cols) --}}
        <div class="lg:col-span-5 card p-6 space-y-5">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-800">Keamanan Akun</h3>
                <p class="text-xs text-slate-500 mt-0.5">Ubah kata sandi akun Anda secara berkala untuk menjaga keamanan.</p>
            </div>

            <form action="{{ url('/profile/password') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700">Password Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="password" name="current_password" required class="input" placeholder="••••••••">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700">Password Baru <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required class="input" placeholder="Minimal 6 karakter">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-700">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required class="input" placeholder="Ulangi password baru">
                </div>

                <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-3 text-[11px] text-indigo-700 space-y-1">
                    <div class="font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                        Tips Keamanan
                    </div>
                    <div>Gunakan kombinasi huruf besar, huruf kecil, dan angka untuk menjaga keamanan akun Anda.</div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="btn bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-5">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
