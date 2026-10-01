@extends('layouts.user')

@section('page-title', 'Semua Notifikasi')

@section('user-content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">
        <h2 class="card-title"><i class="fa-solid fa-bell"></i> Notifikasi Saya</h2>
        @php
            $unreadCount = Auth::user()->unreadNotifications->count();
        @endphp
        @if($unreadCount > 0)
            <form action="{{ route('user.notifications.read_all') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-primary" style="font-size: 0.85rem; padding: 6px 12px;">
                    <i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="card-body">
        @if($notifications->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($notifications as $notif)
                    @php
                        $isUnread = is_null($notif->read_at);
                        $status = $notif->data['status'] ?? 'info';
                        
                        $icon = 'fa-circle-info';
                        $color = 'var(--text-muted)';
                        
                        if ($status === 'approved' || $status === 'selesai' || $status === 'disetujui') {
                            $icon = 'fa-circle-check';
                            $color = '#10b981';
                        } elseif ($status === 'rejected' || $status === 'ditolak' || $status === 'perlu_perbaikan') {
                            $icon = 'fa-circle-xmark';
                            $color = '#ef4444';
                        } elseif ($status === 'cancelled' || $status === 'dibatalkan') {
                            $icon = 'fa-ban';
                            $color = '#f59e0b';
                        }
                    @endphp

                    <a href="{{ route('user.notifications.read', $notif->id) }}" style="display: flex; gap: 20px; padding: 20px 24px; background: {{ $isUnread ? 'rgba(16, 185, 129, 0.05)' : 'var(--bg-dark)' }}; border: 1px solid {{ $isUnread ? 'rgba(16, 185, 129, 0.2)' : 'var(--border-color)' }}; border-radius: 12px; text-decoration: none; transition: all 0.2s ease; align-items: flex-start;">
                        <div style="color: {{ $color }}; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; background: rgba(255,255,255,0.03); border-radius: 50%; flex-shrink: 0;">
                            <i class="fa-solid {{ $icon }}"></i>
                        </div>
                        <div style="flex-grow: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 15px;">
                                <h4 style="margin: 0; font-size: 1.05rem; color: {{ $isUnread ? '#fff' : 'var(--text-sub)' }}; font-weight: {{ $isUnread ? '700' : '600' }}; line-height: 1.4;">{{ $notif->data['title'] ?? 'Notifikasi' }}</h4>
                                <span style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; margin-top: 3px;"><i class="fa-regular fa-clock"></i> {{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p style="margin: 0 0 16px 0; font-size: 0.9rem; color: var(--text-muted); line-height: 1.6;">
                                {{ $notif->data['message'] ?? '' }}
                            </p>
                            @if(isset($notif->data['request_number']) && !empty($notif->data['request_number']))
                                <div style="font-size: 0.75rem; display: inline-block; padding: 5px 10px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 6px; color: var(--primary); font-weight: 600; letter-spacing: 0.05em;">
                                    <i class="fa-solid fa-hashtag"></i> {{ $notif->data['request_number'] }}
                                </div>
                            @endif
                        </div>
                        @if($isUnread)
                            <div style="display: flex; align-items: center;">
                                <div style="width: 10px; height: 10px; background: var(--primary); border-radius: 50%; box-shadow: 0 0 10px var(--primary);"></div>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <div style="margin-top: 20px;">
                {{ $notifications->links('vendor.pagination.custom') }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; background: rgba(255,255,255,0.02);">
                <i class="fa-regular fa-bell-slash" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: var(--text-sub);">Tidak Ada Notifikasi</h3>
                <p style="margin: 0; font-size: 0.9rem;">Anda belum memiliki notifikasi apapun saat ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
