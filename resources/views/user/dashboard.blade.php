@extends('layouts.user')

@section('page-title', 'Dashboard Anggota')

@section('user-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Welcome Card -->
    <div class="glass-card" style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.2) 0%, rgba(19, 27, 46, 0.9) 100%); border-color: rgba(16, 185, 129, 0.3); padding: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="font-size: 0.85rem; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Sistem Informasi Personalia</span>
                <h1 style="font-size: 1.8rem; color: #fff; margin: 4px 0 8px 0;">Selamat Datang, {{ $user->name }}</h1>
                <p style="color: var(--text-sub); font-size: 0.95rem;">
                    NRP/NIP: <strong style="color: #fff;">{{ $user->personel->nrp_nip ?? '-' }}</strong> | Pangkat: <strong style="color: #fff;">{{ $user->personel->pangkat_golongan ?? '-' }}</strong> | Jabatan: <strong style="color: #fff;">{{ $user->personel->jabatan ?? '-' }}</strong>
                </p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('user.leave.create') }}" class="btn-military">
                    <i class="fa-solid fa-plus"></i> Ajukan Cuti
                </a>
                <a href="{{ route('user.marriage.create') }}" class="btn-secondary" style="background: rgba(217, 119, 6, 0.2); border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
                    <i class="fa-solid fa-heart"></i> Ajukan Nikah
                </a>
            </div>
        </div>
    </div>

    <!-- Visual Kartu Jatah Cuti Tahunan (Section 9 Requirement) -->
    <div>
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-chart-pie" style="color: var(--primary);"></i> STATISTIK JATAH CUTI TAHUNAN ({{ date('Y') }})
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
            <!-- Card 1: Jatah Awal -->
            <div class="glass-card" style="position: relative; overflow: hidden;">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(255, 255, 255, 0.03);">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">CUTI TAHUNAN</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #fff; margin: 12px 0 4px 0;">{{ $entitlement['total'] }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">Hari</span></div>
                <div style="font-size: 0.85rem; color: var(--text-sub);">Jatah resmi per tahun kerja</div>
            </div>

            <!-- Card 2: Cuti Digunakan -->
            <div class="glass-card" style="position: relative; overflow: hidden; border-color: rgba(59, 130, 246, 0.3);">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(59, 130, 246, 0.05);">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.05em;">TELAH DIGUNAKAN</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #93c5fd; margin: 12px 0 4px 0;">{{ $entitlement['used'] }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">Hari</span></div>
                <div style="font-size: 0.85rem; color: var(--text-sub);">Total cuti yang disetujui</div>
            </div>

            <!-- Card 3: Sedang Diajukan -->
            <div class="glass-card" style="position: relative; overflow: hidden; border-color: rgba(245, 158, 11, 0.3);">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(245, 158, 11, 0.05);">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.05em;">SEDANG DIAJUKAN</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #fde047; margin: 12px 0 4px 0;">{{ $entitlement['pending'] }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">Hari</span></div>
                <div style="font-size: 0.85rem; color: var(--text-sub);">Menunggu verifikasi admin</div>
            </div>

            <!-- Card 4: Sisa Cuti Tersedia -->
            <div class="glass-card" style="position: relative; overflow: hidden; background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, var(--glass-bg) 100%); border-color: rgba(16, 185, 129, 0.4);">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(16, 185, 129, 0.08);">
                    <i class="fa-solid fa-shield-heart"></i>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #34d399; text-transform: uppercase; letter-spacing: 0.05em;">SISA CUTI TERSEDIA</div>
                <div style="font-size: 2.4rem; font-weight: 800; color: #6ee7b7; margin: 12px 0 4px 0;">{{ $entitlement['available_after_pending'] }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">Hari</span></div>
                <div style="font-size: 0.85rem; color: var(--text-sub);">Dapat diajukan kembali</div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Recent Submissions & Shortcuts -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

        <!-- Left Column: Recent Leave Requests -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 1.1rem; color: #fff; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-clock-history" style="color: var(--primary);"></i> Pengajuan Cuti Terbaru
                </h3>
                <a href="{{ route('user.leave.index') }}" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">Lihat Semua &rarr;</a>
            </div>

            @if($recentLeaveRequests->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                    <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
                    <p>Belum ada pengajuan cuti yang dikirim.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Jenis</th>
                                <th>Tanggal Cuti</th>
                                <th>Hari Kerja</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLeaveRequests as $req)
                                <tr>
                                    <td style="font-weight: 600; color: #fff;">{{ $req->request_number }}</td>
                                    <td>{{ $req->leaveType->name }}</td>
                                    <td>{{ $req->start_date->format('d M Y') }} - {{ $req->end_date->format('d M Y') }}</td>
                                    <td><strong style="color: #34d399;">{{ $req->status === 'approved' ? ($req->approved_days ?? $req->working_days_count) : $req->working_days_count }} Hari</strong></td>
                                    <td>
                                        @if($req->status === 'pending')
                                            <span class="badge badge-pending">PENDING</span>
                                        @elseif($req->status === 'approved')
                                            <span class="badge badge-approved">DISETUJUI</span>
                                        @elseif($req->status === 'rejected')
                                            <span class="badge badge-rejected">DITOLAK</span>
                                        @else
                                            <span class="badge badge-cancelled">DIBATALKAN</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.leave.show', $req->id) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right Column: Shortcuts & Quick Info -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Shortcut Menu -->
            <div class="glass-card">
                <h4 style="font-size: 1rem; color: #fff; margin-bottom: 16px;">Shortcut Pintar</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('user.leave.create') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 8px;"></i> Ajukan Cuti Baru</span>
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                    </a>
                    <a href="{{ route('user.marriage.create') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="fa-solid fa-heart" style="color: var(--accent-gold); margin-right: 8px;"></i> Ajukan Izin Nikah</span>
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                    </a>
                    <a href="{{ route('user.leave.index') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="fa-solid fa-list-check" style="color: #60a5fa; margin-right: 8px;"></i> Riwayat Pengajuan</span>
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                    </a>
                    <a href="{{ route('user.profile') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: space-between;">
                        <span><i class="fa-solid fa-user-gear" style="color: #a78bfa; margin-right: 8px;"></i> Kelola Profil</span>
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                    </a>
                </div>
            </div>

            <!-- Hari Libur Terdekat -->
            <div class="glass-card">
                <h4 style="font-size: 1rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-star" style="color: var(--accent-gold);"></i> Libur Nasional {{ date('Y') }}
                </h4>
                <div style="display: flex; flex-direction: column; gap: 12px; max-height: 250px; overflow-y: auto;">
                    @foreach($holidays->take(4) as $h)
                        <div style="padding: 10px 12px; background: rgba(255, 255, 255, 0.03); border-radius: 8px; border-left: 3px solid var(--primary);">
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
