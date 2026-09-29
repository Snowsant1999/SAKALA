@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Ajukan Aspirasi & Keluhan Fasilitas')
@section('page-title', 'Layanan Aspirasi — Form Pengajuan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 animate-fade-in-up">
    {{-- Info Banner --}}
    <div class="bg-gradient-to-r from-navy-800 to-indigo-900 p-6 rounded-2xl text-white shadow-xl flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
            <svg class="w-6 h-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.83M11.42 15.17l-3.96 3.96a2.653 2.653 0 01-3.75-3.75l3.96-3.96m5.75 3.75l-5.75-5.75M10.5 4.5l3 3m-3-3l-3 3m3-3v8.25" /></svg>
        </div>
        <div class="space-y-1">
            <h2 class="text-base font-bold text-white">Layanan Aspirasi & Keluhan Sarana Prasarana</h2>
            <p class="text-xs text-slate-300 leading-relaxed">
                Laporkan kendala fasilitas seperti AC tidak dingin, proyektor mati, kursi/meja rusak, masalah kebersihan, atau usulan perbaikan sarana belajar demi kenyamanan bersama.
            </p>
        </div>
    </div>

    {{-- Aspiration Form Card --}}
    <div class="card p-8">
        <form action="{{ url('/aspirations') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Category & Location --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <label class="form-label" for="category">Kategori Aspirasi / Keluhan</label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="Kerusakan Fasilitas">Kerusakan Fasilitas Ruang (AC/Lampu/Pintu)</option>
                        <option value="Sarana Kelas">Sarana Belajar (Proyektor/Whiteboard/Audio)</option>
                        <option value="Koneksi Internet">Koneksi Internet & WiFi Kampus</option>
                        <option value="Kebersihan">Kebersihan Ruangan & Toilet</option>
                        <option value="Layanan Akademik">Saran Layanan Akademik</option>
                        <option value="Lainnya">Usulan & Aspirasi Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="location">Lokasi / Ruangan Spesifik</label>
                    <input type="text" name="location" id="location" class="form-input" placeholder="Contoh: Lab Multimedia TI Lt. 1, Ruang 2A..." required>
                </div>
            </div>

            {{-- Chronology & Description --}}
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi Kendala / Saran</label>
                <textarea name="description" id="description" rows="5" class="form-input" placeholder="Jelaskan kendala fasilitas yang Anda temukan atau usulan perbaikan secara rinci..." required></textarea>
            </div>

            {{-- Submit Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ url('/aspirations') }}" class="btn btn-secondary text-xs">Batal</a>
                <button type="submit" class="btn btn-primary text-xs px-6">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                    Kirim Aspirasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
