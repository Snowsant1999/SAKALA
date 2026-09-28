@extends('layouts.admin')

@section('title', 'Master Data Lantai')
@section('page-title', 'Data Lantai')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ url('/admin/floors') }}" class="flex items-center gap-3">
            <label for="building-filter" class="text-xs font-semibold text-slate-600">Gedung</label>
            <select id="building-filter" name="building_id" class="form-select text-xs" onchange="this.form.submit()">
                <option value="">Semua Gedung</option>
                @foreach($buildings as $building)
                    <option value="{{ $building->id }}" @selected((string) request('building_id') === (string) $building->id)>{{ $building->name }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ url('/admin/floors/create') }}" class="btn btn-primary text-xs">Tambah Lantai</a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Gedung</th>
                        <th>Lantai</th>
                        <th>Jumlah Ruangan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($floors as $floor)
                        <tr>
                            <td class="text-xs font-semibold text-slate-700">{{ $floor['building'] }}</td>
                            <td class="text-xs text-slate-700">{{ $floor['label'] }}</td>
                            <td class="text-xs text-slate-600">{{ $floor['rooms_count'] }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ url('/admin/floors/'.$floor['id'].'/edit') }}" class="text-xs font-semibold text-indigo-600">Edit</a>
                                    <form method="POST" action="{{ url('/admin/floors/'.$floor['id']) }}" onsubmit="return confirm('Hapus lantai ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-sm text-slate-500">Belum ada data lantai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
