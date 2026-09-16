@extends('layouts.admin')

@section('page-title', 'Kelola Pengajuan Izin Nikah')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Title & Filters -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengelolaan Permohonan Izin Nikah Personel</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Verifikasi kelengkapan berkas administrasi dan persetujuan surat izin nikah</p>
        </div>
    </div>

    <!-- Filters Glass Card -->
    <div class="glass-card" style="padding: 20px;">
        <form action="{{ route('admin.marriage.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; align-items: end;">
            <div>
                <label class="form-label" for="search">Cari Nama Personel / Pasangan / NRP</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Ketik kata kunci pencarian..." value="{{ request('search') }}">
            </div>

            <div>
                <label class="form-label" for="status">Status Verifikasi</label>
                <select id="status" name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-military" style="flex-grow: 1; background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-card">
        @if($marriageRequests->isEmpty())
            <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-heart-crack" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
                <p>Belum ada pengajuan izin nikah yang ditemukan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Nama Personel</th>
                            <th>Calon Pasangan</th>
                            <th>Rencana Nikah</th>
                            <th>Lokasi</th>
                            <th>Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marriageRequests as $req)
                            <tr>
                                <td><strong style="color: #fff;">{{ $req->request_number }}</strong></td>
                                <td>
                                    <strong style="color: #fff;">{{ $req->user->name }}</strong>
                                    <div style="font-size: 0.775rem; color: var(--accent-gold);">{{ $req->user->personel->pangkat_golongan ?? '' }}</div>
                                </td>
                                <td><strong style="color: #fbbf24;">{{ $req->spouse_name }}</strong></td>
                                <td>{{ $req->marriage_date->format('d F Y') }}</td>
                                <td>{{ $req->marriage_location }}</td>
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
                                    <a href="{{ route('admin.marriage.show', $req->id) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.825rem;">
                                        <i class="fa-solid fa-file-signature"></i> Verifikasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $marriageRequests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
