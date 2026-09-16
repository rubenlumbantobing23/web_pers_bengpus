@extends('layouts.user')

@section('page-title', 'Pengajuan Cuti Saya')

@section('user-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Daftar Riwayat Pengajuan Cuti</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola dan pantau status seluruh pengajuan cuti Anda</p>
        </div>

        <a href="{{ route('user.leave.create') }}" class="btn-military">
            <i class="fa-solid fa-plus-circle"></i> Buat Pengajuan Cuti Baru
        </a>
    </div>

    <!-- Summary Badges -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="glass-card" style="padding: 18px;">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Sisa Jatah Cuti</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #34d399; margin-top: 4px;">{{ $entitlement['available_after_pending'] }} <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-muted);">Hari Kerja</span></div>
        </div>
        <div class="glass-card" style="padding: 18px;">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Cuti Telah Disetujui</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #60a5fa; margin-top: 4px;">{{ $entitlement['used'] }} <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-muted);">Hari Kerja</span></div>
        </div>
        <div class="glass-card" style="padding: 18px;">
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Pengajuan Menunggu</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #fbbf24; margin-top: 4px;">{{ $entitlement['pending'] }} <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-muted);">Hari Kerja</span></div>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="glass-card">
        @if($leaveRequests->isEmpty())
            <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.4;"></i>
                <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 6px;">Belum Ada Pengajuan Cuti</h4>
                <p style="margin-bottom: 20px;">Anda belum pernah membuat pengajuan cuti di sistem ini.</p>
                <a href="{{ route('user.leave.create') }}" class="btn-military">
                    <i class="fa-solid fa-plus-circle"></i> Buat Pengajuan Cuti Pertama
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Hari Kerja</th>
                            <th>Status Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $req)
                            <tr>
                                <td>
                                    <strong style="color: #fff;">{{ $req->request_number }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Diajukan pada {{ $req->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: #f8fafc;">{{ $req->leaveType->name }}</span>
                                </td>
                                <td>{{ $req->start_date->format('d M Y') }}</td>
                                <td>{{ $req->end_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge" style="background: rgba(5, 150, 105, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                                        {{ $req->status === 'approved' ? ($req->approved_days ?? $req->working_days_count) : $req->working_days_count }} Hari
                                    </span>
                                </td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge badge-pending"><i class="fa-solid fa-clock"></i> PENDING</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> DISETUJUI</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge badge-rejected"><i class="fa-solid fa-circle-xmark"></i> DITOLAK</span>
                                    @else
                                        <span class="badge badge-cancelled"><i class="fa-solid fa-ban"></i> DIBATALKAN</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('user.leave.show', $req->id) }}" class="btn-secondary" style="padding: 8px 14px; font-size: 0.85rem;">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
