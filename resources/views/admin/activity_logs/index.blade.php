@extends('layouts.admin')

@section('page-title', 'Log Aktivitas')

@section('admin-content')
<div class="glass-card" style="padding: 24px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 4px;">Log Aktivitas Sistem</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Riwayat aktivitas pengguna di dalam sistem untuk kebutuhan audit.</p>
        </div>
    </div>

    <form action="{{ route('admin.activity_logs.index') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <div class="input-wrapper" style="position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas, deskripsi, atau nama user..." 
                    style="width: 100%; padding: 10px 16px 10px 40px; background: rgba(0, 0, 0, 0.2); border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 0.9rem;">
                <i class="fa-solid fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
            </div>
        </div>
        <div style="width: 200px;">
            <select name="action_filter" style="width: 100%; padding: 10px 16px; background: rgba(0, 0, 0, 0.2); border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 0.9rem; appearance: none;">
                <option value="" style="color: #000;">Semua Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action_filter') == $act ? 'selected' : '' }} style="color: #000;">{{ $act }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary" style="padding: 10px 20px;">
            Filter
        </button>
        @if(request('search') || request('action_filter'))
            <a href="{{ route('admin.activity_logs.index') }}" class="btn-secondary" style="padding: 10px 20px;">
                Reset
            </a>
        @endif
    </form>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <th style="text-align: left; padding: 12px 16px; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Waktu</th>
                    <th style="text-align: left; padding: 12px 16px; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">User</th>
                    <th style="text-align: left; padding: 12px 16px; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Aksi</th>
                    <th style="text-align: left; padding: 12px 16px; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Deskripsi</th>
                    <th style="text-align: left; padding: 12px 16px; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 16px; font-size: 0.9rem; color: #fff; white-space: nowrap;">
                        {{ $log->created_at->format('d M Y H:i:s') }}
                    </td>
                    <td style="padding: 16px; font-size: 0.9rem; color: #fff;">
                        @if($log->user)
                            <div style="font-weight: 600;">{{ $log->user->name }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $log->user->email }}</div>
                        @else
                            <span style="color: var(--text-muted); font-style: italic;">Sistem / Anonymous</span>
                        @endif
                    </td>
                    <td style="padding: 16px;">
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.8rem;">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td style="padding: 16px; font-size: 0.9rem; color: var(--text-main);">
                        {{ $log->description ?: '-' }}
                    </td>
                    <td style="padding: 16px; font-size: 0.85rem; color: var(--text-muted); font-family: monospace;">
                        {{ $log->ip_address ?: '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        Belum ada data log aktivitas yang sesuai pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $logs->links('pagination::tailwind') }}
    </div>
</div>
@endsection
