@extends('layouts.admin')

@section('page-title', 'Detail Pengguna Aplikasi')

@section('admin-content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">{{ $user->name }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Terdaftar sejak {{ $user->created_at->format('d F Y H:i') }} WIB</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Info Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Akun Web -->
        <div class="glass-card">
            <h3 style="font-size: 1.05rem; color: #fff; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-user" style="color: #60a5fa;"></i> Akun Aplikasi
            </h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Nama Akun:</span>
                    <div style="font-weight: 700; color: #fff;">{{ $user->name }}</div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Email Login:</span>
                    <div style="color: #60a5fa; font-weight: 600;">{{ $user->email }}</div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Jatah Cuti Tahun {{ date('Y') }}:</span>
                    <div style="font-weight: 700; color: #34d399;">
                        {{ $entitlement['available_after_pending'] }} / {{ $entitlement['total'] }} Hari Kerja Tersisa
                    </div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Total Pengajuan Cuti:</span>
                    <div style="font-weight: 700; color: #fff;">{{ $user->leaveRequests->count() }} Pengajuan</div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Total Pengajuan Nikah:</span>
                    <div style="font-weight: 700; color: #fff;">{{ $user->marriageRequests->count() }} Pengajuan</div>
                </div>
            </div>
        </div>

        <!-- Data Personel Terhubung -->
        <div class="glass-card">
            <h3 style="font-size: 1.05rem; color: #fff; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-id-card-clip" style="color: #34d399;"></i> Data Personel Terhubung
            </h3>
            @if($user->personel)
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">Jenis Personel:</span>
                        <div style="margin-top: 3px;">
                            @if($user->personel->jenis_personel === 'militer')
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(96,165,250,0.12); color: #60a5fa; border: 1px solid rgba(96,165,250,0.3); border-radius: 6px; padding: 3px 10px; font-size: 0.8rem; font-weight: 700;">
                                    <i class="fa-solid fa-shield-halved"></i> MILITER (TNI)
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); border-radius: 6px; padding: 3px 10px; font-size: 0.8rem; font-weight: 700;">
                                    <i class="fa-solid fa-briefcase"></i> PNS / SIPIL
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">NRP / NIP:</span>
                        <div style="font-weight: 700; color: var(--accent-gold);">{{ $user->personel->nrp_nip }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">Pangkat / Golongan:</span>
                        <div style="color: #fff;">{{ $user->personel->pangkat_golongan }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">Jabatan:</span>
                        <div style="color: #fff;">{{ $user->personel->jabatan }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">Satuan / Bagian:</span>
                        <div style="color: #fff;">{{ $user->personel->satuan_bagian }}</div>
                    </div>
                </div>
            @else
                <div style="text-align: center; padding: 30px 20px; color: var(--text-muted);">
                    <i class="fa-solid fa-id-card-clip" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="font-size: 0.875rem;">Akun ini belum terhubung ke data nominatif personel.</p>
                    <a href="{{ route('admin.personel.index') }}" class="btn-secondary" style="margin-top: 10px; font-size: 0.8rem;">
                        Kelola Nominatif
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Cuti -->
    <div class="glass-card">
        <h3 style="font-size: 1.05rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-calendar-check" style="color: #34d399;"></i> Riwayat Pengajuan Cuti
        </h3>

        @if($user->leaveRequests->isEmpty())
            <p style="color: var(--text-muted); font-size: 0.875rem; text-align: center; padding: 20px;">Belum ada pengajuan cuti.</p>
        @else
            <div class="table-responsive">
                <table class="custom-table" style="font-size: 0.875rem;">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Jenis Cuti</th>
                            <th>Periode</th>
                            <th>Hari Diajukan</th>
                            <th>Hari Disetujui Kabengpus</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->leaveRequests->sortByDesc('created_at') as $lr)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.leave.show', $lr->id) }}" style="color: var(--accent-gold); font-weight: 700; text-decoration: underline;">
                                        {{ $lr->request_number }}
                                    </a>
                                </td>
                                <td>{{ $lr->leaveType->name ?? '—' }}</td>
                                <td style="font-size: 0.8rem;">
                                    {{ $lr->start_date->format('d M Y') }} – {{ $lr->end_date->format('d M Y') }}
                                </td>
                                <td style="font-weight: 700; color: #60a5fa;">{{ $lr->working_days_count }} Hari</td>
                                <td>
                                    @if($lr->status === 'approved' && $lr->approved_days)
                                        <span style="font-weight: 800; color: #34d399;">
                                            {{ $lr->approved_days }} Hari
                                        </span>
                                        @if($lr->approved_days < $lr->working_days_count)
                                            <span style="font-size: 0.72rem; color: #f59e0b; display: block;">(Sebagian)</span>
                                        @endif
                                    @else
                                        <span style="color: var(--text-muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lr->status === 'pending')
                                        <span class="badge badge-pending" style="font-size: 0.72rem;">PENDING</span>
                                    @elseif($lr->status === 'approved')
                                        <span class="badge badge-approved" style="font-size: 0.72rem;">DISETUJUI</span>
                                    @elseif($lr->status === 'rejected')
                                        <span class="badge badge-rejected" style="font-size: 0.72rem;">DITOLAK</span>
                                    @else
                                        <span class="badge badge-cancelled" style="font-size: 0.72rem;">DIBATALKAN</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
