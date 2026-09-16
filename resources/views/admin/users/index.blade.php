@extends('layouts.admin')

@section('page-title', 'Pengguna Aplikasi')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengguna Aplikasi Web</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Daftar anggota yang telah mendaftar / aktif menggunakan sistem</p>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(96, 165, 250, 0.25), rgba(59, 130, 246, 0.1)); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                <i class="fa-solid fa-users" style="color: #60a5fa;"></i>
            </div>
            <div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #fff;">{{ $totalUsers }}</div>
                <div style="font-size: 0.775rem; color: var(--text-muted);">Total Pengguna Terdaftar</div>
            </div>
        </div>
        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(52, 211, 153, 0.25), rgba(16, 185, 129, 0.1)); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                <i class="fa-solid fa-shield-halved" style="color: #34d399;"></i>
            </div>
            <div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #fff;">{{ $totalMiliter }}</div>
                <div style="font-size: 0.775rem; color: var(--text-muted);">Personel Militer</div>
            </div>
        </div>
        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(217, 119, 6, 0.1)); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                <i class="fa-solid fa-briefcase" style="color: #fbbf24;"></i>
            </div>
            <div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #fff;">{{ $totalPns }}</div>
                <div style="font-size: 0.775rem; color: var(--text-muted);">Personel PNS</div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card" style="padding: 18px;">
        <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 16px; align-items: flex-end;">
            <div style="flex: 1;">
                <label class="form-label" for="search">Cari Nama / Email / NRP</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-military">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="btn-secondary" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass-card">
        @if($users->isEmpty())
            <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
                <p>Tidak ada pengguna ditemukan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama / Email</th>
                            <th>NRP / NIP</th>
                            <th>Status Relasi</th>
                            <th>Jabatan</th>
                            <th>Terdaftar</th>
                            <th>Cuti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td>
                                    <div>
                                        <strong style="color: #fff;">{{ $u->name }}</strong>
                                        <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $u->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: var(--accent-gold);">{{ $u->personel->nrp_nip }}</strong>
                                </td>
                                <td>
                                    @if($u->personel->jenis_personel === 'militer')
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: rgba(96,165,250,0.12); color: #60a5fa; border: 1px solid rgba(96,165,250,0.3); border-radius: 6px; padding: 2px 7px; font-size: 0.72rem; font-weight: 700;">
                                            <i class="fa-solid fa-shield-halved"></i> MILITER
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); border-radius: 6px; padding: 2px 7px; font-size: 0.72rem; font-weight: 700;">
                                            <i class="fa-solid fa-briefcase"></i> PNS
                                        </span>
                                    @endif
                                </td>
                                <td style="font-size: 0.875rem;">{{ $u->personel->jabatan ?? '—' }}</td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $u->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <span style="background: rgba(52, 211, 153, 0.12); color: #34d399; border-radius: 6px; padding: 3px 8px; font-size: 0.8rem; font-weight: 700;">
                                        {{ $u->leave_requests_count }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <a href="{{ route('admin.users.show', $u->id) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">{{ $users->links() }}</div>
        @endif
    </div>

</div>

@endsection
