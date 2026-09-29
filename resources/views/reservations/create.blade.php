@extends(session('user_role') === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Form Pengajuan Reservasi Ruangan')
@section('page-title')
<div class="flex items-center gap-3">
    <a href="{{ session('user_role') === 'admin' ? url('/admin/reservations') : url('/rooms') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
    </a>
    <span>Form Pengajuan Reservasi Ruangan</span>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6 animate-fade-in-up">
    {{-- Info Alert Box --}}
    <div class="bg-indigo-50/80 border border-indigo-200 p-5 rounded-2xl flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-navy-600 text-white flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
        </div>
        <div class="text-xs text-navy-950 space-y-1">
            <div class="font-bold text-sm text-navy-900">Ketentuan Pengajuan Reservasi</div>
            <p class="leading-relaxed text-slate-600">
                Pengajuan ini bersifat <strong>REQUEST (Menunggu Persetujuan Admin)</strong>. Slot ruangan baru resmi terpakai setelah Admin Akademik melakukan verifikasi dan memberikan status <strong>APPROVED</strong>.
            </p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card p-8">
        <form action="{{ url('/reservations') }}" method="POST" class="space-y-6">
            @csrf

            {{-- 1. Room Details Section --}}
            <div class="pb-6 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-navy-50 text-navy-700 text-xs font-bold flex items-center justify-center">1</span>
                    Detail Ruangan Terpilih
                </h3>

                @if($room)
                    <input type="hidden" name="room_id" value="{{ old('room_id', $room->id) }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div><label class="form-label text-xs">Gedung</label><input type="text" class="form-input bg-slate-50 font-medium" value="{{ $room->building?->name }}" readonly></div>
                        <div><label class="form-label text-xs">Lantai</label><input type="text" class="form-input bg-slate-50 font-medium" value="{{ $room->floorRecord?->label ?? 'Lantai '.$room->floor }}" readonly></div>
                        <div><label class="form-label text-xs">Ruangan</label><input type="text" class="form-input bg-slate-50 font-bold text-navy-700" value="{{ $room->name }}" readonly></div>
                    </div>
                @else
                    <div>
                        <label class="form-label text-xs" for="room_id">Ruangan</label>
                        <select name="room_id" id="room_id" class="form-select" required>
                            <option value="">Pilih ruangan</option>
                            @foreach($rooms as $availableRoom)
                                <option value="{{ $availableRoom->id }}" @selected((string) old('room_id') === (string) $availableRoom->id)>{{ $availableRoom->building?->name }} · {{ $availableRoom->floorRecord?->label ?? 'Lantai '.$availableRoom->floor }} · {{ $availableRoom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            {{-- 2. Date & Time Section --}}
            <div class="pb-6 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-navy-50 text-navy-700 text-xs font-bold flex items-center justify-center">2</span>
                    Waktu Penggunaan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label text-xs" for="date">Tanggal Kegiatan</label>
                        <input type="date" name="date" id="date" class="form-input" min="{{ today()->toDateString() }}" value="{{ old('date', $date) }}" required>
                    </div>
                    <div>
                        <label class="form-label text-xs" for="start_time">Jam Mulai</label>
                        <input type="time" name="start_time" id="start_time" class="form-input" value="{{ old('start_time', $startTime) }}" required>
                    </div>
                    <div>
                        <label class="form-label text-xs" for="end_time">Jam Selesai</label>
                        <input type="time" name="end_time" id="end_time" class="form-input" value="{{ old('end_time', $endTime) }}" required>
                    </div>
                </div>
            </div>

            {{-- 3. Academic Context & Purpose --}}
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-navy-50 text-navy-700 text-xs font-bold flex items-center justify-center">3</span>
                    Konteks Akademik & Tujuan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label text-xs" for="course_id">Mata Kuliah Terkait</label>
                        <select name="course_id" id="course_id" class="form-select">
                            <option value="">Kegiatan umum</option>
                            @foreach($courses as $c)
                                <option value="{{ $c['id'] }}" @selected((string) old('course_id') === (string) $c['id'])>{{ $c['name'] }} ({{ $c['code'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs" for="course_class_id">Kelas</label>
                        <select name="course_class_id" id="course_class_id" class="form-select">
                            <option value="">Tidak terkait kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class['id'] }}" data-course-id="{{ $class['course_id'] }}" @selected((string) old('course_class_id') === (string) $class['id'])>{{ $class['name'] }} · {{ $class['course'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label text-xs" for="purpose">Tujuan & Urgensi Penggunaan</label>
                    <textarea name="purpose" id="purpose" rows="3" class="form-input" placeholder="Jelaskan kegiatan yang akan dilakukan..." required>{{ old('purpose') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label text-xs" for="notes">Catatan Tambahan untuk Admin (Opsional)</label>
                    <textarea name="notes" id="notes" rows="2" class="form-input" placeholder="Tuliskan peralatan khusus atau catatan tambahan...">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Submit Actions --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ url('/rooms') }}" class="btn btn-secondary text-xs">Batal</a>
                <button type="submit" class="btn btn-primary text-xs px-6">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>
                    Kirim Pengajuan Reservasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
