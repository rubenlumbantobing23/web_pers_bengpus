@extends('layouts.user')

@section('page-title', 'Dashboard Anggota')

@section('user-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Welcome Card -->
    <div class="glass-card" style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.2) 0%, rgba(19, 27, 46, 0.9) 100%); border-color: rgba(16, 185, 129, 0.3); padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="font-size: 0.85rem; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Beranda Sistem Informasi Personalia</span>
                <h1 style="font-size: 1.6rem; color: #fff; margin: 4px 0 8px 0;">Selamat Datang, {{ $user->name }}</h1>
                <p style="color: var(--text-sub); font-size: 0.95rem; margin: 0;">
                    NRP/NIP: <strong style="color: #fff;">{{ $user->personel->nrp_nip ?? '-' }}</strong> | Pangkat: <strong style="color: #fff;">{{ $user->personel->pangkat_golongan ?? '-' }}</strong> | Jabatan: <strong style="color: #fff;">{{ $user->personel->jabatan ?? '-' }}</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Layanan Personalia Section -->
    <div>
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i> LAYANAN PERSONALIA
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div class="glass-card" style="padding: 20px; display: flex; flex-direction: column; gap: 12px; border-color: rgba(16, 185, 129, 0.2);">
                <div style="font-size: 1.5rem; color: var(--primary);"><i class="fa-solid fa-umbrella-beach"></i></div>
                <h4 style="color: #fff; font-size: 1.1rem; margin: 0;">Pengajuan Cuti</h4>
                <p style="color: var(--text-sub); font-size: 0.85rem; margin: 0; flex-grow: 1;">Ajukan dan kelola permohonan cuti tahunan, sakit, atau alasan penting.</p>
                <a href="{{ route('user.leave.create') }}" class="btn-primary" style="text-align: center; font-size: 0.9rem; padding: 8px;">Ajukan Cuti</a>
            </div>
            
            <div class="glass-card" style="padding: 20px; display: flex; flex-direction: column; gap: 12px; border-color: rgba(217, 119, 6, 0.2);">
                <div style="font-size: 1.5rem; color: var(--accent-gold);"><i class="fa-solid fa-heart"></i></div>
                <h4 style="color: #fff; font-size: 1.1rem; margin: 0;">Pengajuan Nikah</h4>
                <p style="color: var(--text-sub); font-size: 0.85rem; margin: 0; flex-grow: 1;">Ajukan dan pantau proses izin nikah secara terpusat.</p>
                <a href="{{ route('user.pengajuan_nikah.create') }}" class="btn-secondary" style="text-align: center; font-size: 0.9rem; padding: 8px; background: rgba(217, 119, 6, 0.1); border-color: rgba(245, 158, 11, 0.3); color: #fbbf24;">Ajukan Nikah</a>
            </div>

            <div class="glass-card" style="padding: 20px; display: flex; flex-direction: column; gap: 12px; border-color: rgba(59, 130, 246, 0.2);">
                <div style="font-size: 1.5rem; color: #60a5fa;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <h4 style="color: #fff; font-size: 1.1rem; margin: 0;">Riwayat Pengajuan</h4>
                <p style="color: var(--text-sub); font-size: 0.85rem; margin: 0; flex-grow: 1;">Lihat seluruh riwayat pengajuan personalia Anda.</p>
                <a href="{{ route('user.leave.index') }}" class="btn-secondary" style="text-align: center; font-size: 0.9rem; padding: 8px; border-color: rgba(59, 130, 246, 0.3); color: #93c5fd;">Lihat Riwayat</a>
            </div>
        </div>
    </div>

    <!-- General Stats -->
    <div>
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-chart-line" style="color: var(--primary);"></i> RINGKASAN AKTIVITAS
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
            <div class="glass-card" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(255, 255, 255, 0.03);">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Total Pengajuan</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #fff; margin: 12px 0 4px 0;">{{ $totalRequests }}</div>
            </div>

            <div class="glass-card" style="position: relative; overflow: hidden; border-color: rgba(245, 158, 11, 0.3);">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(245, 158, 11, 0.05);">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.05em;">Sedang Diproses</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #fde047; margin: 12px 0 4px 0;">{{ $totalActive }}</div>
            </div>

            <div class="glass-card" style="position: relative; overflow: hidden; border-color: rgba(16, 185, 129, 0.4);">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(16, 185, 129, 0.08);">
                    <i class="fa-solid fa-check-double"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #34d399; text-transform: uppercase; letter-spacing: 0.05em;">Disetujui</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #6ee7b7; margin: 12px 0 4px 0;">{{ $approvedRequests }}</div>
            </div>

            <div class="glass-card" style="position: relative; overflow: hidden; border-color: rgba(239, 68, 68, 0.3);">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(239, 68, 68, 0.05);">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #f87171; text-transform: uppercase; letter-spacing: 0.05em;">Notifikasi Baru</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #fca5a5; margin: 12px 0 4px 0;">{{ $unreadNotifications }}</div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Recent Activities & Info Panel -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

        <!-- Left Column: Aktivitas Terbaru -->
        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-bolt" style="color: var(--primary);"></i> Aktivitas Terbaru
            </h3>

            @if($activities->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                    <i class="fa-solid fa-inbox" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
                    <p>Belum ada aktivitas tercatat.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($activities as $activity)
                        <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border-left: 3px solid var(--primary);">
                            <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                @if($activity['type'] == 'Cuti')
                                    <i class="fa-solid fa-umbrella-beach"></i>
                                @else
                                    <i class="fa-solid fa-heart"></i>
                                @endif
                            </div>
                            <div style="flex-grow: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <h5 style="color: #fff; font-size: 0.95rem; margin: 0;">{{ $activity['title'] }}</h5>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px; font-size: 0.85rem;">
                                    @if($activity['status'] === 'pending')
                                        <span style="color: #fbbf24;"><i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Sedang Diproses</span>
                                    @elseif($activity['status'] === 'approved')
                                        <span style="color: #34d399;"><i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Disetujui</span>
                                    @elseif($activity['status'] === 'rejected')
                                        <span style="color: #f87171;"><i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Ditolak</span>
                                    @else
                                        <span style="color: var(--text-muted);"><i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Dibatalkan</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ $activity['url'] }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                Detail
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Informasi Personalia -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <div class="glass-card">
                <h4 style="font-size: 1rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-info" style="color: #60a5fa;"></i> Informasi Personalia
                </h4>
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Sisa Cuti -->
                    <div style="padding: 16px; background: rgba(59, 130, 246, 0.05); border-radius: 12px; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <div style="font-size: 0.8rem; color: var(--text-sub); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Sisa Cuti Tahunan</div>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            <span style="font-size: 1.8rem; font-weight: 700; color: #fff;">{{ $entitlement['available_after_pending'] }}</span>
                            <span style="font-size: 0.9rem; color: var(--text-muted);">Hari</span>
                        </div>
                        <a href="{{ route('user.leave.index') }}" style="font-size: 0.8rem; color: #60a5fa; text-decoration: none; margin-top: 8px; display: inline-block;">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Hari Libur Terdekat -->
            <div class="glass-card">
                <h4 style="font-size: 1rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-star" style="color: var(--accent-gold);"></i> Libur Nasional {{ date('Y') }}
                </h4>
                <div style="display: flex; flex-direction: column; gap: 12px; max-height: 250px; overflow-y: auto;">
                    @foreach($holidays->take(4) as $h)
                        <div style="padding: 10px 12px; background: rgba(255, 255, 255, 0.03); border-radius: 8px; border-left: 3px solid var(--accent-gold);">
                            <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">{{ $h->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($h->date)->format('d F Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
        </div>

    </div>

</div>
@endsection
