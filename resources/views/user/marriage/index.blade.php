@extends('layouts.user')

@section('page-title', 'Pengajuan Nikah')

@section('user-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengajuan Izin Nikah Anggota</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola dan pantau permohonan izin menikah di lingkungan Bengpuskomlekad</p>
        </div>

        @if(Auth::user()->personel && Auth::user()->personel->status_pernikahan === 'Menikah')
            <div style="background-color: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 4px; color: #fca5a5; font-size: 0.9rem;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i>
                Data Anda tercatat sudah berstatus <strong>Menikah</strong>. Anda tidak dapat mengajukan izin nikah baru.
            </div>
        @else
            <a href="{{ route('user.marriage.create') }}" class="btn-military" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                <i class="fa-solid fa-heart"></i> Buat Pengajuan Nikah Baru
            </a>
        @endif
    </div>

    <!-- Marriage Requests Table -->
    <div class="glass-card">
        @if($marriageRequests->isEmpty())
            <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-heart-crack" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.4;"></i>
                <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 6px;">Belum Ada Pengajuan Izin Nikah</h4>
                <p style="margin-bottom: 20px;">Anda belum pernah mengirimkan pengajuan permohonan izin menikah.</p>
                @if(!(Auth::user()->personel && Auth::user()->personel->status_pernikahan === 'Menikah'))
                    <a href="{{ route('user.marriage.create') }}" class="btn-military" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                        <i class="fa-solid fa-heart"></i> Pengajuan Izin Nikah
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Nama Pasangan</th>
                            <th>Tanggal Rencana Nikah</th>
                            <th>Lokasi Akad / Resepsi</th>
                            <th>Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marriageRequests as $req)
                            <tr>
                                <td>
                                    <strong style="color: #fff;">{{ $req->request_number }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $req->created_at->format('d/m/Y') }}</div>
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
                                    <a href="{{ route('user.marriage.show', $req->id) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                        Detail
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
