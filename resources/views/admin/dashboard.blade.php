@extends('layouts.admin')

@section('page-title', 'Dashboard Monitoring Personalia')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Welcome Banner -->
    <div class="glass-card" style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.2) 0%, rgba(19, 27, 46, 0.9) 100%); border-color: rgba(245, 158, 11, 0.3); padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <span style="font-size: 0.85rem; color: var(--accent-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Staf Personalia Bengpuskomlekad</span>
                <h1 style="font-size: 1.8rem; color: #fff; margin: 4px 0 4px 0;">Dashboard Monitoring Personalia</h1>
                <p style="color: var(--text-sub); font-size: 0.925rem; margin: 0;">Pantau aktivitas, pengajuan, personel, dan layanan administrasi personalia secara terpadu.</p>
            </div>
        </div>
    </div>

    <!-- 4 KPI Monitoring Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <!-- Card 1: Total Personel -->
        <div class="glass-card" style="padding: 20px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(255, 255, 255, 0.03);"><i class="fa-solid fa-users"></i></div>
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">TOTAL PERSONEL</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #fff; margin-top: 6px;">{{ $totalPersonel }}</div>
        </div>

        <!-- Card 2: Total Pengajuan -->
        <div class="glass-card" style="padding: 20px; position: relative; overflow: hidden; border-color: rgba(59, 130, 246, 0.3);">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(59, 130, 246, 0.05);"><i class="fa-solid fa-file-signature"></i></div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #60a5fa; text-transform: uppercase;">TOTAL PENGAJUAN</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #93c5fd; margin-top: 6px;">{{ $totalPengajuan }}</div>
        </div>

        <!-- Card 3: Menunggu Tindakan -->
        <div class="glass-card" style="padding: 20px; position: relative; overflow: hidden; border-color: rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.08);">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(245, 158, 11, 0.05);"><i class="fa-solid fa-hourglass-half"></i></div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #fbbf24; text-transform: uppercase;">SEDANG DIPROSES</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #fde047; margin-top: 6px;">{{ $totalPending }}</div>
        </div>

        <!-- Card 4: Aktivitas -->
        <div class="glass-card" style="padding: 20px; position: relative; overflow: hidden; border-color: rgba(16, 185, 129, 0.4);">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(16, 185, 129, 0.05);"><i class="fa-solid fa-bolt"></i></div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #34d399; text-transform: uppercase;">Aktivitas Baru (7 Hari)</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #6ee7b7; margin-top: 6px;">{{ $recentActivityCount }}</div>
        </div>
    </div>

    <!-- Monitoring Layanan -->
    <div>
        <h3 style="font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
            <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i> Monitoring Layanan
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
            
            <!-- Pengajuan Cuti -->
            <div class="glass-card" style="padding: 20px; border-top: 3px solid var(--primary);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="color: #fff; font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-umbrella-beach" style="color: var(--primary);"></i> Pengajuan Cuti
                    </h4>
                    <a href="{{ route('admin.leave.index') }}" style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">Lihat Pengajuan &rarr;</a>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: rgba(255,255,255,0.03); padding: 12px; border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total</div>
                        <div style="font-size: 1.4rem; color: #fff; font-weight: 700;">{{ $leaveStats['total'] }}</div>
                    </div>
                    <div style="background: rgba(245,158,11,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #fbbf24;">
                        <div style="font-size: 0.75rem; color: #fbbf24; text-transform: uppercase;">Menunggu</div>
                        <div style="font-size: 1.4rem; color: #fde047; font-weight: 700;">{{ $leaveStats['pending'] }}</div>
                    </div>
                    <div style="background: rgba(16,185,129,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #34d399;">
                        <div style="font-size: 0.75rem; color: #34d399; text-transform: uppercase;">Disetujui</div>
                        <div style="font-size: 1.4rem; color: #6ee7b7; font-weight: 700;">{{ $leaveStats['approved'] }}</div>
                    </div>
                    <div style="background: rgba(239,68,68,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #f87171;">
                        <div style="font-size: 0.75rem; color: #f87171; text-transform: uppercase;">Ditolak</div>
                        <div style="font-size: 1.4rem; color: #fca5a5; font-weight: 700;">{{ $leaveStats['rejected'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Pengajuan Nikah -->
            <div class="glass-card" style="padding: 20px; border-top: 3px solid var(--accent-gold);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="color: #fff; font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-heart" style="color: var(--accent-gold);"></i> Pengajuan Nikah
                    </h4>
                    <a href="{{ route('admin.admin.pengajuan_nikah.index') }}" style="font-size: 0.8rem; color: var(--accent-gold); font-weight: 600;">Lihat Pengajuan &rarr;</a>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: rgba(255,255,255,0.03); padding: 12px; border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total</div>
                        <div style="font-size: 1.4rem; color: #fff; font-weight: 700;">{{ $marriageStats['total'] }}</div>
                    </div>
                    <div style="background: rgba(245,158,11,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #fbbf24;">
                        <div style="font-size: 0.75rem; color: #fbbf24; text-transform: uppercase;">Menunggu</div>
                        <div style="font-size: 1.4rem; color: #fde047; font-weight: 700;">{{ $marriageStats['pending'] }}</div>
                    </div>
                    <div style="background: rgba(16,185,129,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #34d399;">
                        <div style="font-size: 0.75rem; color: #34d399; text-transform: uppercase;">Disetujui</div>
                        <div style="font-size: 1.4rem; color: #6ee7b7; font-weight: 700;">{{ $marriageStats['approved'] }}</div>
                    </div>
                    <div style="background: rgba(239,68,68,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #f87171;">
                        <div style="font-size: 0.75rem; color: #f87171; text-transform: uppercase;">Ditolak</div>
                        <div style="font-size: 1.4rem; color: #fca5a5; font-weight: 700;">{{ $marriageStats['rejected'] }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        
        <!-- Aktivitas Terbaru -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 1.1rem; color: #fff; display: flex; align-items: center; gap: 10px; margin: 0;">
                    <i class="fa-solid fa-list-timeline" style="color: #60a5fa;"></i> Aktivitas Terbaru
                </h3>
                <a href="{{ route('admin.activity_logs.index') }}" style="font-size: 0.85rem; color: #60a5fa; font-weight: 600;">Lihat Semua &rarr;</a>
            </div>

            @if($recentActivities->isEmpty())
                <div style="text-align: center; padding: 20px; color: var(--text-muted);">Belum ada aktivitas tercatat.</div>
            @else
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($recentActivities as $log)
                        <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px; background: rgba(255, 255, 255, 0.02); border-radius: 8px; border-left: 2px solid #60a5fa;">
                            <div style="flex-shrink: 0; margin-top: 4px; color: #60a5fa;">
                                <i class="fa-solid fa-circle-dot" style="font-size: 0.8rem;"></i>
                            </div>
                            <div style="flex-grow: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="color: #fff; font-size: 0.9rem;">{{ $log->user->name ?? 'Sistem' }}</strong>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-sub); margin-top: 2px;">{{ $log->action }}</div>
                                @if($log->description)
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; font-style: italic;">"{{ $log->description }}"</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Akses Cepat -->
        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-rocket" style="color: #a78bfa;"></i> Akses Cepat
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('admin.personel.create') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between; text-align: left;">
                    <span><i class="fa-solid fa-user-plus" style="color: var(--primary); margin-right: 8px; width: 20px;"></i> Tambah Personel</span>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                </a>
                <a href="{{ route('admin.letters.index') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between; text-align: left;">
                    <span><i class="fa-solid fa-envelope-open-text" style="color: var(--accent-gold); margin-right: 8px; width: 20px;"></i> Arsip Surat Intern</span>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                </a>
                <a href="{{ route('admin.templates.index') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between; text-align: left;">
                    <span><i class="fa-solid fa-file-word" style="color: #60a5fa; margin-right: 8px; width: 20px;"></i> Kelola Template</span>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                </a>
                <a href="{{ route('admin.organization.index') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between; text-align: left;">
                    <span><i class="fa-solid fa-sitemap" style="color: #a78bfa; margin-right: 8px; width: 20px;"></i> Struktur & Pejabat</span>
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
