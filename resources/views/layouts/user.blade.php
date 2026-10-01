@extends('layouts.app')

@section('content')
<div style="display: flex; min-height: 100vh;">
    <!-- User Sidebar Navigation -->
    <aside style="width: 280px; background: var(--bg-sidebar); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;">
        <a href="{{ route('user.dashboard') }}" style="padding: 24px 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px; text-decoration: none; cursor: pointer;">
            <div class="emblem-icon" style="width: 45px; height: 45px; flex-shrink: 0; background: transparent; display: flex; align-items: center; justify-content: center; padding: 0;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Bengpus" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <div>
                <h3 style="font-size: 1.1rem; color: #fff; line-height: 1.2;">BENGPUSKOMLEKAD</h3>
                <span style="font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Portal Anggota</span>
            </div>
        </a>

        <nav style="padding: 20px 14px; flex-grow: 1; display: flex; flex-direction: column; gap: 6px;">
            <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house-user"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('user.leave.index') }}" class="nav-item {{ request()->routeIs('user.leave.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Pengajuan Cuti</span>
            </a>

            <a href="{{ route('user.pengajuan_nikah.index') }}" class="nav-item {{ request()->routeIs('user.pengajuan_nikah.*') ? 'active' : '' }}">
                <i class="fa-solid fa-heart"></i>
                <span>Pengajuan Nikah</span>
            </a>

            <a href="{{ route('user.profile') }}" class="nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-id-card"></i>
                <span>Profil Saya</span>
            </a>
        </nav>

        <div style="padding: 20px; border-top: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding: 10px; background: rgba(255, 255, 255, 0.03); border-radius: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #059669, #047857); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div style="overflow: hidden;">
                    <div style="font-weight: 600; font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ Auth::user()->personel->pangkat_golongan ?? 'Anggota' }}</div>
                </div>
            </div>

            <form id="logout-form-user" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" onclick="confirmLogout('logout-form-user')" class="btn-secondary" style="width: 100%; justify-content: center; padding: 10px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar (Logout)
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div style="margin-left: 280px; flex-grow: 1; display: flex; flex-direction: column; min-height: 100vh;">
        <header style="height: 70px; border-bottom: 1px solid var(--border-color); background: rgba(9, 13, 24, 0.8); backdrop-filter: blur(10px); padding: 0 30px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 90;">
            <div style="font-size: 1.1rem; font-weight: 700; color: #fff;">
                @yield('page-title', 'Dashboard')
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                @php
                    $unreadNotifications = Auth::user()->unreadNotifications;
                    $unreadCount = $unreadNotifications->count();
                    $latestNotifications = Auth::user()->notifications()->latest()->take(5)->get();
                @endphp
                
                <div class="notification-wrapper" style="position: relative;">
                    <button id="notifBtn" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-muted); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; transition: all 0.3s ease;">
                        <i class="fa-solid fa-bell"></i>
                        @if($unreadCount > 0)
                            <span style="position: absolute; top: -4px; right: -4px; background: #ef4444; color: white; font-size: 0.65rem; font-weight: bold; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 2px solid var(--bg-dark);">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </button>
                    
                    <!-- Dropdown -->
                    <div id="notifDropdown" style="display: none; position: absolute; top: 50px; right: 0; width: 320px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); z-index: 100; overflow: hidden;">
                        <div style="padding: 15px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; font-size: 0.9rem; color: #fff; font-weight: 600;">Notifikasi</h4>
                            @if($unreadCount > 0)
                                <form action="{{ route('user.notifications.read_all') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: var(--primary); font-size: 0.75rem; cursor: pointer; font-weight: 600; padding: 0;">Tandai Semua Dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @forelse($latestNotifications as $notif)
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
                                <a href="{{ route('user.notifications.read', $notif->id) }}" style="display: flex; gap: 12px; padding: 12px 15px; border-bottom: 1px solid var(--border-color); text-decoration: none; transition: background 0.2s; background: {{ $isUnread ? 'rgba(16, 185, 129, 0.05)' : 'transparent' }};">
                                    <div style="color: {{ $color }}; font-size: 1.1rem; margin-top: 2px;">
                                        <i class="fa-solid {{ $icon }}"></i>
                                    </div>
                                    <div style="flex-grow: 1; overflow: hidden;">
                                        <div style="font-size: 0.8rem; font-weight: {{ $isUnread ? '700' : '600' }}; color: {{ $isUnread ? '#fff' : 'var(--text-sub)' }}; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $notif->data['title'] ?? 'Notifikasi' }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $notif->data['message'] ?? '' }}</div>
                                        <div style="font-size: 0.65rem; color: var(--text-sub); display: flex; justify-content: space-between;">
                                            <span>{{ isset($notif->data['request_number']) ? $notif->data['request_number'] : '' }}</span>
                                            <span>{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    @if($isUnread)
                                        <div style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; align-self: center; flex-shrink: 0;"></div>
                                    @endif
                                </a>
                            @empty
                                <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                                    Belum ada notifikasi.
                                </div>
                            @endforelse
                        </div>
                        <div style="padding: 10px; border-top: 1px solid var(--border-color); text-align: center;">
                            <a href="{{ route('user.notifications.index') }}" style="color: var(--primary); font-size: 0.8rem; font-weight: 600; text-decoration: none;">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const notifBtn = document.getElementById('notifBtn');
                        const notifDropdown = document.getElementById('notifDropdown');
                        
                        if(notifBtn && notifDropdown) {
                            notifBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                const isVisible = notifDropdown.style.display === 'block';
                                notifDropdown.style.display = isVisible ? 'none' : 'block';
                            });
                            
                            document.addEventListener('click', function(e) {
                                if(!notifDropdown.contains(e.target) && e.target !== notifBtn) {
                                    notifDropdown.style.display = 'none';
                                }
                            });
                        }
                    });
                </script>

                <span class="badge badge-approved">
                    <i class="fa-solid fa-user-shield"></i> {{ Auth::user()->personel->pangkat_golongan ?? 'ANGGOTA' }}
                </span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">
                    <i class="fa-regular fa-calendar-days"></i> {{ date('d M Y') }}
                </span>
            </div>
        </header>

        <main style="padding: 30px; flex-grow: 1;">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('user-content')
        </main>
    </div>
</div>

<style>
    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.925rem;
        transition: all 0.2s ease;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
    }

    .nav-item.active {
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.25) 0%, rgba(5, 150, 105, 0.1) 100%);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .nav-item i {
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }
</style>
@endsection
