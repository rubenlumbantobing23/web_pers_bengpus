@extends('layouts.admin')

@section('page-title', 'Dashboard Monitoring Personalia')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Welcome Banner -->
    <div class="glass-card" style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.2) 0%, rgba(19, 27, 46, 0.9) 100%); border-color: rgba(245, 158, 11, 0.3); padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <span style="font-size: 0.85rem; color: var(--accent-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Staf Personalia Bengpuskomlekad</span>
                <h1 style="font-size: 1.8rem; color: #fff; margin: 4px 0 4px 0;">Dashboard Pengawasan & Verifikasi</h1>
                <p style="color: var(--text-sub); font-size: 0.925rem;">Monitor pengajuan cuti, permohonan nikah, dan data nominatif personel secara terpadu.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('admin.personel.create') }}" class="btn-military">
                    <i class="fa-solid fa-user-plus"></i> Tambah Personel
                </a>
                <a href="{{ route('admin.letters.index') }}" class="btn-secondary">
                    <i class="fa-solid fa-file-arrow-up"></i> Upload Arsip Surat
                </a>
            </div>
        </div>
    </div>

    <!-- 6 KPI Monitoring Cards (Section 14 Requirement) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <!-- Card 1: Total Personel -->
        <div class="glass-card" style="padding: 20px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">TOTAL PERSONEL</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #fff; margin-top: 6px;">{{ $totalPersonel }} <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-muted);">Personel</span></div>
            <div style="font-size: 0.8rem; color: var(--text-sub); margin-top: 4px;">Aktif terdaftar di nominatif</div>
        </div>

        <!-- Card 2: Total Pengajuan Cuti -->
        <div class="glass-card" style="padding: 20px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #60a5fa; text-transform: uppercase;">TOTAL PENGAJUAN CUTI</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #93c5fd; margin-top: 6px;">{{ $totalLeaveRequests }}</div>
            <div style="font-size: 0.8rem; color: var(--text-sub); margin-top: 4px;">Seluruh riwayat cuti</div>
        </div>

        <!-- Card 3: Cuti Pending -->
        <div class="glass-card" style="padding: 20px; border-color: rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.08);">
            <div style="font-size: 0.8rem; font-weight: 700; color: #fbbf24; text-transform: uppercase;">CUTI MENUNGGU VERIFIKASI</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #fde047; margin-top: 6px;">{{ $pendingLeaveRequests }} <span style="font-size: 0.9rem; font-weight: 400; color: #fbbf24;">Pending</span></div>
            <div style="font-size: 0.8rem; color: #fef08a; margin-top: 4px;">Membutuhkan tindakan admin</div>
        </div>

        <!-- Card 4: Cuti Disetujui -->
        <div class="glass-card" style="padding: 20px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #34d399; text-transform: uppercase;">CUTI DISETUJI</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #6ee7b7; margin-top: 6px;">{{ $approvedLeaveRequests }}</div>
            <div style="font-size: 0.8rem; color: var(--text-sub); margin-top: 4px;">Telah diterbitkan permohonan</div>
        </div>

        <!-- Card 5: Total Pengajuan Nikah -->
        <div class="glass-card" style="padding: 20px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #f472b6; text-transform: uppercase;">PENGAJUAN NIKAH</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #fbcfe8; margin-top: 6px;">{{ $totalMarriageRequests }}</div>
            <div style="font-size: 0.8rem; color: var(--text-sub); margin-top: 4px;">Permohonan izin nikah</div>
        </div>

        <!-- Card 6: Nikah Pending -->
        <div class="glass-card" style="padding: 20px; border-color: rgba(244, 114, 182, 0.4); background: rgba(244, 114, 182, 0.08);">
            <div style="font-size: 0.8rem; font-weight: 700; color: #f472b6; text-transform: uppercase;">NIKAH MENUNGGU VERIFIKASI</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #f472b6; margin-top: 6px;">{{ $pendingMarriageRequests }}</div>
            <div style="font-size: 0.8rem; color: #fbcfe8; margin-top: 4px;">Perlu pemeriksaan berkas</div>
        </div>
    </div>

    <!-- Main Monitoring Table -->
    <div class="glass-card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <h3 style="font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Tabel Pengajuan Cuti Terbaru Personel
            </h3>

            <!-- Search & Filter Form -->
            <form action="{{ route('admin.dashboard') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;" id="dashboardFilterForm">
                <input type="text" id="liveSearchInput" name="search" class="form-control" style="width: 220px; padding: 8px 14px;" placeholder="Cari Nama / NRP..." value="{{ request('search') }}">
                
                <select name="status" class="form-control" style="width: 140px; padding: 8px 14px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </form>
        </div>

        @if($recentLeaveRequests->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-inbox" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
                <p>Tidak ditemukan pengajuan cuti yang cocok dengan filter.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama / NRP Anggota</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Cuti</th>
                            <th>Hari Kerja</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th>Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLeaveRequests as $req)
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 0.95rem;">{{ $req->user->name }}</strong>
                                    <div style="font-size: 0.775rem; color: var(--accent-gold);">
                                        {{ $req->user->personel->pangkat_golongan ?? '' }} — {{ $req->user->personel->nrp_nip ?? '' }}
                                    </div>
                                </td>
                                <td>{{ $req->leaveType->name }}</td>
                                <td>{{ $req->start_date->format('d/m/Y') }} - {{ $req->end_date->format('d/m/Y') }}</td>
                                <td><strong style="color: #34d399;">{{ $req->status === 'approved' ? ($req->approved_days ?? $req->working_days_count) : $req->working_days_count }} Hari</strong></td>
                                <td>{{ $req->created_at->format('d M Y H:i') }}</td>
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
                                    <a href="{{ route('admin.leave.show', $req->id) }}" class="btn-military" style="padding: 6px 14px; font-size: 0.8rem;">
                                        <i class="fa-solid fa-magnifying-glass"></i> Periksa / Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $recentLeaveRequests->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('.custom-table tbody tr');
                
                rows.forEach(row => {
                    const nameCell = row.querySelector('td:first-child');
                    if (nameCell) {
                        const text = nameCell.textContent || nameCell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }
        
        // Prevent form submission on enter for the search input to allow live search
        const form = document.getElementById('dashboardFilterForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (document.activeElement === searchInput) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
@endsection
