@extends('layouts.app')

@section('content')
<div style="display: flex; min-height: 100vh;">
    <!-- Admin Sidebar Navigation -->
    <aside style="width: 280px; background: var(--bg-sidebar); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;">
        <a href="{{ route('admin.dashboard') }}" style="padding: 24px 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px; text-decoration: none; cursor: pointer;">
            <div class="emblem-icon" style="width: 45px; height: 45px; flex-shrink: 0; background: transparent; display: flex; align-items: center; justify-content: center; padding: 0;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Bengpus" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <div>
                <h3 style="font-size: 1.1rem; color: #fff; line-height: 1.2;">BENGPUSKOMLEKAD</h3>
                <span style="font-size: 0.75rem; color: var(--accent-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Staf Personalia</span>
            </div>
        </a>

        <nav style="padding: 16px 12px; flex-grow: 1; display: flex; flex-direction: column; gap: 4px; overflow-y: auto;">
            
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard Admin</span>
            </a>

            <a href="{{ route('admin.leave.index') }}" class="nav-item {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Pengajuan Cuti</span>
            </a>

            <a href="{{ route('admin.admin.pengajuan_nikah.index') }}" class="nav-item {{ request()->routeIs('admin.admin.pengajuan_nikah.*') ? 'active' : '' }}">
                <i class="fa-solid fa-heart"></i>
                <span>Pengajuan Nikah</span>
            </a>

            <div class="nav-group" style="margin-top: 10px;">
                @php
                    $isPersonelActive = request()->routeIs('admin.personel.*');
                    $isMiliterActive = $isPersonelActive && request('jenis') === 'militer';
                    $isPnsActive = $isPersonelActive && request('jenis') === 'pns';
                    $isPengelolaanActive = $isPersonelActive || request()->routeIs('admin.organization.*') || request()->routeIs('admin.templates.*');
                @endphp
                
                <a href="#" class="nav-item {{ $isPengelolaanActive ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('pengelolaan-submenu').classList.toggle('show'); document.getElementById('pengelolaan-caret').classList.toggle('fa-chevron-down'); document.getElementById('pengelolaan-caret').classList.toggle('fa-chevron-up');">
                    <i class="fa-solid fa-database"></i>
                    <span style="flex-grow: 1;">Pengelolaan Data</span>
                    <i id="pengelolaan-caret" class="fa-solid {{ $isPengelolaanActive ? 'fa-chevron-up' : 'fa-chevron-down' }}" style="width: auto; font-size: 0.8rem;"></i>
                </a>

                <div id="pengelolaan-submenu" class="submenu {{ $isPengelolaanActive ? 'show' : '' }}">
                    <a href="#" class="submenu-item {{ $isPersonelActive ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('personel-submenu').classList.toggle('show'); document.getElementById('personel-caret').classList.toggle('fa-chevron-down'); document.getElementById('personel-caret').classList.toggle('fa-chevron-up');">
                        <i class="fa-solid fa-id-card-clip"></i>
                        <span style="flex-grow: 1;">Nominatif Personel</span>
                        <i id="personel-caret" class="fa-solid {{ $isPersonelActive ? 'fa-chevron-up' : 'fa-chevron-down' }}" style="width: auto; font-size: 0.7rem;"></i>
                    </a>
                    
                    <div id="personel-submenu" class="submenu {{ $isPersonelActive ? 'show' : '' }}" style="padding-left: 15px; margin-top: 2px;">
                        <a href="{{ route('admin.personel.index', ['jenis' => 'militer']) }}" class="submenu-item {{ $isMiliterActive ? 'active' : '' }}">
                            <i class="fa-solid fa-minus" style="font-size: 0.5rem;"></i> Data Militer
                        </a>
                        <a href="{{ route('admin.personel.index', ['jenis' => 'pns']) }}" class="submenu-item {{ $isPnsActive ? 'active' : '' }}">
                            <i class="fa-solid fa-minus" style="font-size: 0.5rem;"></i> Data PNS
                        </a>
                    </div>
                    <a href="{{ route('admin.organization.index') }}" class="submenu-item {{ request()->routeIs('admin.organization.*') ? 'active' : '' }}" style="margin-top: 8px;">
                        <i class="fa-solid fa-sitemap"></i> Struktur Pejabat
                    </a>
                    
                    <a href="{{ route('admin.templates.index') }}" class="submenu-item {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-word"></i> Template Surat
                    </a>
                </div>
            </div>
            <div class="nav-group" style="margin-top: 10px;">
                @php
                    $isMasterActive = request()->routeIs('admin.activity_logs.*') || 
                                      request()->routeIs('admin.leave_types.*') || 
                                      request()->routeIs('admin.holidays.*');
                @endphp
                <a href="#" class="nav-item {{ $isMasterActive ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('master-submenu').classList.toggle('show'); document.getElementById('master-caret').classList.toggle('fa-chevron-down'); document.getElementById('master-caret').classList.toggle('fa-chevron-up');">
                    <i class="fa-solid fa-gear"></i>
                    <span style="flex-grow: 1;">Konfigurasi Sistem</span>
                    <i id="master-caret" class="fa-solid {{ $isMasterActive ? 'fa-chevron-up' : 'fa-chevron-down' }}" style="width: auto; font-size: 0.8rem;"></i>
                </a>
                
                <div id="master-submenu" class="submenu {{ $isMasterActive ? 'show' : '' }}">
                    <a href="{{ route('admin.activity_logs.index') }}" class="submenu-item {{ request()->routeIs('admin.activity_logs.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i> Log Aktivitas
                    </a>
                    <a href="{{ route('admin.leave_types.index') }}" class="submenu-item {{ request()->routeIs('admin.leave_types.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check"></i> Jenis & Syarat Cuti
                    </a>
                    <a href="{{ route('admin.holidays.index') }}" class="submenu-item {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days"></i> Kalender Libur
                    </a>
                </div>
            </div>

        </nav>

        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            <a href="{{ route('admin.profile') }}" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 10px; background: rgba(245, 158, 11, 0.08); border-radius: 12px; border: 1px solid rgba(245, 158, 11, 0.2); text-decoration: none; transition: background 0.3s ease;" onmouseover="this.style.background='rgba(245, 158, 11, 0.15)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.08)'">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #d97706, #b45309); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div style="overflow: hidden;">
                    <div style="font-weight: 600; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.725rem; color: var(--accent-gold);">ADMIN PERSONALIA</div>
                </div>
            </a>

            <form id="logout-form-admin" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" onclick="confirmLogout('logout-form-admin')" class="btn-secondary" style="width: 100%; justify-content: center; padding: 8px 14px; font-size: 0.85rem;">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar Admin
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div style="margin-left: 280px; flex-grow: 1; display: flex; flex-direction: column; min-height: 100vh; width: calc(100vw - 280px); max-width: calc(100vw - 280px);">
        <header style="height: 70px; border-bottom: 1px solid var(--border-color); background: rgba(9, 13, 24, 0.85); backdrop-filter: blur(10px); padding: 0 30px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 90;">
            <div style="font-size: 1.1rem; font-weight: 700; color: #fff;">
                @yield('page-title', 'Admin Portal Personalia')
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                <!-- Notification Bell -->
                <div style="position: relative;" id="notification-dropdown">
                    <button type="button" onclick="document.getElementById('notif-menu').classList.toggle('show-notif')" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: var(--text-muted); width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; position: relative;" onmouseover="this.style.color='#fff'; this.style.borderColor='var(--accent-gold)';" onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-color)';">
                        <i class="fa-regular fa-bell"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: white; font-size: 0.6rem; font-weight: bold; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid var(--bg-dark);">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Menu -->
                    <div id="notif-menu" style="position: absolute; top: 50px; right: 0; width: 320px; background: var(--bg-sidebar); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display: none; flex-direction: column; z-index: 100; overflow: hidden;">
                        <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.2);">
                            <h4 style="margin: 0; font-size: 0.9rem; color: #fff;">Notifikasi Baru</h4>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <form action="{{ route('admin.notifications.read_all') }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: var(--primary); font-size: 0.75rem; cursor: pointer; padding: 0;">Tandai semua dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @forelse(Auth::user()->unreadNotifications as $notification)
                                <a href="{{ route('admin.notifications.read', $notification->id) }}" style="display: flex; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.8rem;">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <div style="color: #fff; font-size: 0.85rem; font-weight: 500; margin-bottom: 4px; line-height: 1.3;">{{ $notification->data['message'] ?? 'Pengajuan baru' }}</div>
                                        <div style="color: var(--text-muted); font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @empty
                                <div style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                                    <i class="fa-regular fa-bell-slash" style="font-size: 1.5rem; margin-bottom: 8px; opacity: 0.5;"></i><br>
                                    Tidak ada notifikasi baru
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <style>
                    .show-notif {
                        display: flex !important;
                        animation: slideDown 0.2s ease forwards;
                    }
                    @keyframes slideDown {
                        from { opacity: 0; transform: translateY(-10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                </style>
                <!-- Add click outside listener -->
                <script>
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('notification-dropdown');
                        const menu = document.getElementById('notif-menu');
                        if (dropdown && !dropdown.contains(event.target)) {
                            menu.classList.remove('show-notif');
                        }
                    });
                </script>

                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                    <i class="fa-solid fa-user-gear"></i> STAF PERSONALIA
                </span>
            </div>
        </header>

        <main style="padding: 30px; flex-grow: 1;">
            <div class="toast-container" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; pointer-events: none;">
                @if(session('success'))
                    <div class="alert alert-success" style="pointer-events: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.5); backdrop-filter: blur(8px); animation: slideInUp 0.4s ease forwards, fadeOut 0.4s ease forwards 5s;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error" style="pointer-events: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.5); backdrop-filter: blur(8px); animation: slideInUp 0.4s ease forwards, fadeOut 0.4s ease forwards 5s;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error" style="pointer-events: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.5); backdrop-filter: blur(8px); animation: slideInUp 0.4s ease forwards, fadeOut 0.4s ease forwards 5s; background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: 12px; border-radius: 4px;">
                        <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444; margin-right: 8px;"></i>
                        <span style="color: #ef4444; font-weight: 600;">Terdapat kesalahan:</span>
                        <ul style="margin: 8px 0 0 24px; color: #fca5a5; font-size: 0.85rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @yield('admin-content')
        </main>
    </div>
</div>

<style>
    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 10px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
    }

    .nav-item.active {
        background: linear-gradient(135deg, rgba(217, 119, 6, 0.25) 0%, rgba(217, 119, 6, 0.1) 100%);
        color: var(--accent-gold);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .nav-item i {
        font-size: 1rem;
        width: 18px;
        text-align: center;
    }
    
    .nav-group {
        display: flex;
        flex-direction: column;
    }
    
    .submenu {
        display: none;
        flex-direction: column;
        gap: 2px;
        padding-left: 28px;
        margin-top: 4px;
    }
    
    .submenu.show {
        display: flex;
    }
    
    .submenu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-radius: 8px;
        color: var(--text-muted);
        font-size: 0.82rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .submenu-item:hover {
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.03);
    }
    
    .submenu-item.active {
        color: var(--accent-gold);
        font-weight: 600;
        background: rgba(245, 158, 11, 0.05);
    }
    
    .submenu-item i {
        font-size: 0.85rem;
        width: 14px;
        text-align: center;
    }
</style>
@endsection
