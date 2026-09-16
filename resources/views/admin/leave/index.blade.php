@extends('layouts.admin')

@section('page-title', 'Kelola & Verifikasi Pengajuan Cuti')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Title & Filter Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengelolaan Permohonan Cuti Personel</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Verifikasi berkas, persetujuan (approval), dan penolakan pengajuan cuti</p>
        </div>
    </div>

    <!-- Filters Glass Card -->
    <div class="glass-card" style="padding: 20px;">
        <form action="{{ route('admin.leave.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: end;">
            <div>
                <label class="form-label" for="search">Cari Nama / NRP</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Ketik nama atau NRP..." value="{{ request('search') }}">
            </div>

            <div>
                <label class="form-label" for="leave_type_id">Jenis Cuti</label>
                <select id="leave_type_id" name="leave_type_id" class="form-control">
                    <option value="">Semua Jenis Cuti</option>
                    @foreach($leaveTypes as $t)
                        <option value="{{ $t->id }}" {{ request('leave_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" for="status">Status Verifikasi</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan User</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-military" style="flex-grow: 1;">
                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.leave.index') }}" class="btn-secondary" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Leave Requests Table -->
    <div class="glass-card">
        @if($leaveRequests->isEmpty())
            <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
                <p>Tidak ada data pengajuan cuti yang memenuhi kriteria pencarian.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Personel Anggota</th>
                            <th>Jenis Cuti</th>
                            <th>Periode Cuti</th>
                            <th>Hari Kerja</th>
                            <th>Tanggal Kirim</th>
                            <th>Status</th>
                            <th>Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $req)
                            <tr>
                                <td><strong style="color: #fff;">{{ $req->request_number }}</strong></td>
                                <td>
                                    <strong style="color: #fff;">{{ $req->user->name }}</strong>
                                    <div style="font-size: 0.775rem; color: var(--accent-gold);">
                                        {{ $req->user->personel->pangkat_golongan ?? '' }} (NRP: {{ $req->user->personel->nrp_nip ?? '-' }})
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
                                    <a href="{{ route('admin.leave.show', $req->id) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.825rem;">
                                        <i class="fa-solid fa-circle-check"></i> Verifikasi
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
