@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Buat Laporan Kampus Aman')
@section('page-title', 'Kampus Aman — Buat Laporan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 animate-fade-in-up">
    {{-- Confidentiality Banner --}}
    <div class="bg-gradient-to-r from-navy-900 to-navy-950 p-6 rounded-2xl text-white shadow-xl flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
            <svg class="w-6 h-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" /></svg>
        </div>
        <div class="space-y-1">
            <h2 class="text-base font-bold text-white">Privasi & Kerahasiaan Anda Dijamin</h2>
            <p class="text-xs text-slate-300 leading-relaxed">
                Platform Kampus Aman SAKALA dirancang khusus untuk memberikan ruang aman bagi seluruh civitas akademika. Laporan yang Anda kirimkan bersifat <strong>rahasia (confidential)</strong>, hanya dapat diakses oleh Anda dan Satgas Penanganan Kampus yang berwenang.
            </p>
        </div>
    </div>

    {{-- Report Form Card --}}
    <div class="card p-8">
        <form action="{{ url('/reports') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Category & Date --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <label class="form-label" for="category">Kategori Insiden / Laporan</label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="Intimidasi">Intimidasi / Ancaman</option>
                        <option value="Perundungan">Perundungan (Bullying)</option>
                        <option value="Pelecehan Verbal">Pelecehan Verbal</option>
                        <option value="Pelecehan Non-Verbal">Pelecehan Non-Verbal / Fisik</option>
                        <option value="Kekerasan">Tindakan Kekerasan</option>
                        <option value="Lainnya">Lainnya / Pelanggaran Etika</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="incident_date">Tanggal & Perkiraan Waktu Kejadian</label>
                    <input type="date" name="incident_date" id="incident_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            {{-- Location & Involved Parties --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <label class="form-label" for="location">Lokasi Kejadian</label>
                    <input type="text" name="location" id="location" class="form-input" placeholder="Contoh: Gedung TI Lantai 2, Kantin, Koridor..." required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="involved_parties">Pihak yang Terlibat (Opsional)</label>
                    <input type="text" name="involved_parties" id="involved_parties" class="form-input" placeholder="Nama / Inisial / Ciri pihak terlapor jika diketahui">
                </div>
            </div>

            {{-- Chronology & Description --}}
            <div class="form-group">
                <label class="form-label" for="description">Kronologi / Deskripsi Kejadian Secara Rinci</label>
                <textarea name="description" id="description" rows="5" class="form-input" placeholder="Ceritakan apa yang terjadi, kapan, bagaimana situasi kejadian, dan dampak yang Anda rasakan secara jelas..." required></textarea>
            </div>

            {{-- Mock Attachment Upload --}}
            <div class="form-group">
                <label class="form-label">Bukti Pendukung / Lampiran (Mock Upload)</label>
                <input type="text" name="attachments" class="form-input" placeholder="Nama berkas bukti (mis. bukti_chat.png / rekaman.mp3)" value="bukti_lampiran_insiden_{{ date('dmY') }}.pdf">
                <p class="text-[11px] text-slate-400 mt-1">Simulasi upload dokumen bukti (tangkapan layar chat, foto, rekaman suara, atau dokumen pendukung).</p>
            </div>

            {{-- Security Notice --}}
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                <span>Identitas pelapor terenkripsi dan dijaga kerahasiaannya oleh sistem.</span>
            </div>

            {{-- Submit Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ url('/reports') }}" class="btn btn-secondary text-xs">Batal</a>
                <button type="submit" class="btn btn-primary text-xs px-6">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                    Kirim Laporan Secara Aman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
