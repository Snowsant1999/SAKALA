@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Overview')

@section('nav-actions')
    <span class="text-sm text-slate-500 mr-2">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
@endsection

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach($stats as $stat)
    <div class="stat-card">
        <div class="stat-icon" style="background: {{ $stat['color'] }}">
            @if($stat['icon'] == 'academic')
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10v6M2 10l10-5 10 5-10 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 12v5c0 1.657 2.686 3 6 3s6-1.343 6-3v-5" /></svg>
            @elseif($stat['icon'] == 'users')
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
            @elseif($stat['icon'] == 'building')
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" /></svg>
            @elseif($stat['icon'] == 'clock')
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            @elseif($stat['icon'] == 'calendar')
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
            @elseif($stat['icon'] == 'shield')
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286ZM12 15h.008v.008H12V15Z" /></svg>
            @elseif($stat['icon'] == 'alert')
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2.25m0 2.25h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" /></svg>
            @else
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2.25m0 2.25h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" /></svg>
            @endif
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stat['value'] }}</div>
            <div class="stat-label">{{ $stat['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Reservation Requests --}}
    <div class="card">
        <div class="card-header border-b-0 pb-0">
            <h3 class="card-title">Pengajuan Reservasi</h3>
            <a href="{{ url('/admin/reservations') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Kelola Semua</a>
        </div>
        <div class="card-body">
            
            {{-- Conflict Indicator --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2.25m0 2.25h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" /></svg>
                <div>
                    <h4 class="text-sm font-semibold text-amber-800">1 Konflik Terdeteksi</h4>
                    <p class="text-xs text-amber-700 mt-0.5">Terdapat 2 request untuk ruangan dan waktu yang sama. Membutuhkan keputusan Anda.</p>
                </div>
                <a href="{{ url('/admin/reservations') }}" class="ml-auto btn btn-sm bg-amber-100 text-amber-700 hover:bg-amber-200 border-0">Review</a>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Requester</th>
                            <th>Ruangan</th>
                            <th>Waktu</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingReservations as $rsv)
                        <tr class="{{ $loop->index < 2 ? 'bg-amber-50/30' : '' }}">
                            <td>
                                <div class="font-medium text-slate-800">{{ $rsv['requester'] }}</div>
                                <div class="text-xs text-slate-500">{{ $rsv['role'] }} • {{ $rsv['class'] }}</div>
                            </td>
                            <td>
                                <div class="text-sm text-slate-700">{{ $rsv['room'] }}</div>
                            </td>
                            <td>
                                <div class="text-xs font-medium text-slate-700">{{ \Carbon\Carbon::parse($rsv['date'])->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $rsv['time'] }}</div>
                            </td>
                            <td class="text-right">
                                <a href="{{ url('/reservations/' . ($rsv['id'] ?? 'rsv-001')) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Campus Safety Reports --}}
    <div class="card">
        <div class="card-header border-b-0 pb-0">
            <h3 class="card-title">Laporan Kampus Aman</h3>
            <a href="{{ url('/admin/reports') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua</a>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategori & Tanggal</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReports as $report)
                        <tr>
                            <td class="font-medium text-slate-600 text-xs">#{{ explode('-', $report['id'])[1] }}</td>
                            <td>
                                <div class="font-medium text-slate-800">{{ $report['category'] }}</div>
                                <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($report['date'])->translatedFormat('d F Y') }}</div>
                            </td>
                            <td>
                                @if($report['status'] == 'SUBMITTED')
                                    <span class="badge badge-gray text-[10px]">Submitted</span>
                                @elseif($report['status'] == 'UNDER_REVIEW')
                                    <span class="badge badge-info text-[10px]">Under Review</span>
                                @else
                                    <span class="badge badge-primary text-[10px]">{{ str_replace('_', ' ', $report['status']) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($report['priority'] == 'HIGH')
                                    <span class="badge badge-warning text-[10px]">High</span>
                                @elseif($report['priority'] == 'MEDIUM')
                                    <span class="badge badge-primary text-[10px]">Medium</span>
                                @else
                                    <span class="badge badge-gray text-[10px]">Low</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ url('/reports/' . $report['id']) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
