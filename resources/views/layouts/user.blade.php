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

            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari aplikasi?');">
                @csrf
                <button type="submit" class="btn-secondary" style="width: 100%; justify-content: center; padding: 10px;">
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
