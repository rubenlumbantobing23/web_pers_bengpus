@extends('layouts.app')

@section('content')
<div style="display: flex; min-height: 100vh;">
    <!-- Admin Sidebar Navigation -->
    <aside style="width: 280px; background: var(--bg-sidebar); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;">
        <a href="{{ route('admin.dashboard') }}" style="padding: 24px 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px; text-decoration: none; cursor: pointer;">
            <div class="emblem-icon" style="width: 40px; height: 40px; font-size: 1.1rem; flex-shrink: 0; background: linear-gradient(135deg, #d97706, #f59e0b);">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h3 style="font-size: 1.1rem; color: #fff; line-height: 1.2;">BENGPUSKOMLEKAD</h3>
                <span style="font-size: 0.75rem; color: var(--accent-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Staf Personalia</span>
            </div>
        </a>

        <nav style="padding: 16px 12px; flex-grow: 1; display: flex; flex-direction: column; gap: 4px; overflow-y: auto;">
            <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; padding: 8px 12px 4px 12px;">Monitoring</div>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard Admin</span>
            </a>

            <a href="{{ route('admin.leave.index') }}" class="nav-item {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Pengajuan Cuti</span>
            </a>

            <a href="{{ route('admin.marriage.index') }}" class="nav-item {{ request()->routeIs('admin.marriage.*') ? 'active' : '' }}">
                <i class="fa-solid fa-heart"></i>
                <span>Pengajuan Nikah</span>
            </a>

            <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; padding: 14px 12px 4px 12px;">Pengelolaan Data</div>

            <div class="nav-group">
                @php
                    $isPersonelActive = request()->routeIs('admin.personel.*');
                    $isMiliterActive = $isPersonelActive && request('jenis') === 'militer';
                    $isPnsActive = $isPersonelActive && request('jenis') === 'pns';
                @endphp
                <a href="#" class="nav-item {{ $isPersonelActive ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('personel-submenu').classList.toggle('show'); document.getElementById('personel-caret').classList.toggle('fa-chevron-down'); document.getElementById('personel-caret').classList.toggle('fa-chevron-up');">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <span style="flex-grow: 1;">Nominatif Personel</span>
                    <i id="personel-caret" class="fa-solid {{ $isPersonelActive ? 'fa-chevron-up' : 'fa-chevron-down' }}" style="width: auto; font-size: 0.8rem;"></i>
                </a>
                <div id="personel-submenu" class="submenu {{ $isPersonelActive ? 'show' : '' }}">
                    <a href="{{ route('admin.personel.index', ['jenis' => 'militer']) }}" class="submenu-item {{ $isMiliterActive ? 'active' : '' }}">
                        <i class="fa-solid fa-minus"></i> Data Militer
                    </a>
                    <a href="{{ route('admin.personel.index', ['jenis' => 'pns']) }}" class="submenu-item {{ $isPnsActive ? 'active' : '' }}">
                        <i class="fa-solid fa-minus"></i> Data PNS
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.activity_logs.index') }}" class="nav-item {{ request()->routeIs('admin.activity_logs.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i>
                <span>Log Aktivitas</span>
            </a>

            <a href="{{ route('admin.letters.index') }}" class="nav-item {{ request()->routeIs('admin.letters.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Arsip Surat Intern</span>
            </a>

            <a href="{{ route('admin.templates.index') }}" class="nav-item {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-word"></i>
                <span>Pengaturan Template</span>
            </a>

            <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; padding: 14px 12px 4px 12px;">Pengaturan Master</div>

            <a href="{{ route('admin.leave_types.index') }}" class="nav-item {{ request()->routeIs('admin.leave_types.*') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders"></i>
                <span>Jenis & Syarat Cuti</span>
            </a>

            <a href="{{ route('admin.holidays.index') }}" class="nav-item {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Kalender / Libur</span>
            </a>

            <a href="{{ route('admin.organization.index') }}" class="nav-item {{ request()->routeIs('admin.organization.*') ? 'active' : '' }}">
                <i class="fa-solid fa-diagram-project"></i>
                <span>Struktur & Pejabat</span>
            </a>
        </nav>

        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 10px; background: rgba(245, 158, 11, 0.08); border-radius: 12px; border: 1px solid rgba(245, 158, 11, 0.2);">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #d97706, #b45309); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700;">
                    A
                </div>
                <div style="overflow: hidden;">
                    <div style="font-weight: 600; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.725rem; color: var(--accent-gold);">ADMIN PERSONALIA</div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari aplikasi?');">
                @csrf
                <button type="submit" class="btn-secondary" style="width: 100%; justify-content: center; padding: 8px 14px; font-size: 0.85rem;">
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
                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                    <i class="fa-solid fa-user-gear"></i> STAF PERSONALIA
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
        font-size: 0.5rem;
        width: 10px;
    }
</style>
@endsection
