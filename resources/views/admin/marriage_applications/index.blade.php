@extends('layouts.admin')

@section('page-title', 'Daftar Pengajuan Nikah')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.4rem; color: #fff; font-weight: 700;">Pengajuan Nikah Anggota</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">Total: {{ $applications->count() }} pengajuan</p>
        </div>
    </div>

    {{-- Status Counters --}}
    @php
        $statusCounts = $applications->groupBy('status')->map->count();
    @endphp
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px;">
        @foreach(['DRAFT' => '#94a3b8', 'DIAJUKAN' => '#60a5fa', 'PERLU_PERBAIKAN' => '#fbbf24', 'DIVERIFIKASI' => '#a78bfa', 'DISETUJUI' => '#34d399', 'SELESAI' => '#10b981', 'DITOLAK' => '#f87171'] as $status => $color)
        <div class="glass-card" style="padding: 12px; text-align: center; border-left: 3px solid {{ $color }};">
            <div style="font-size: 1.8rem; font-weight: 800; color: {{ $color }};">{{ $statusCounts[$status] ?? 0 }}</div>
            <div style="font-size: 0.65rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px;">{{ str_replace('_', ' ', $status) }}</div>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="glass-card">
        @if($applications->isEmpty())
            <div style="text-align: center; padding: 60px; color: var(--text-muted);">
                <i class="fa-solid fa-ring" style="font-size: 3rem; opacity: 0.3; margin-bottom: 16px;"></i>
                <div>Belum ada pengajuan nikah dari anggota.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Pengajuan</th>
                        <th>Nama Anggota</th>
                        <th>Pangkat / NRP</th>
                        <th>Rencana Nikah</th>
                        <th>Calon Pasangan</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $i => $app)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $app->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td>
                            <div style="font-weight: 600; color: #fff;">{{ $app->personel->nama ?? ($app->user->name ?? '-') }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $app->peran_anggota }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">{{ $app->personel->pangkat_golongan ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $app->personel->nrp_nip ?? '-' }}</div>
                        </td>
                        <td>{{ $app->tanggal_rencana_nikah->format('d M Y') }}</td>
                        <td>{{ $app->partner->nama ?? '<em style="color:var(--text-muted)">Belum diisi</em>' }}</td>
                        <td>
                            @php
                                $docsCount    = $app->documents()->count();
                                $diterimaDocs = $app->documents()->where('status_verifikasi','DITERIMA')->count();
                                $ditolakDocs  = $app->documents()->where('status_verifikasi','DITOLAK')->count();
                            @endphp
                            <div style="font-size: 0.82rem;">
                                <span style="color: #34d399;"><i class="fa-solid fa-check"></i> {{ $diterimaDocs }}</span>
                                @if($ditolakDocs > 0)
                                    <span style="color: #f87171; margin-left: 6px;"><i class="fa-solid fa-xmark"></i> {{ $ditolakDocs }}</span>
                                @endif
                                <span style="color: var(--text-muted); margin-left: 6px;">/ {{ $docsCount }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusColors = ['DRAFT'=>'badge-secondary','DIAJUKAN'=>'badge-info','PERLU_PERBAIKAN'=>'badge-warning','DIVERIFIKASI'=>'badge-primary','DISETUJUI'=>'badge-success','SELESAI'=>'badge-success','DITOLAK'=>'badge-danger'];
                            @endphp
                            <span class="badge {{ $statusColors[$app->status] ?? 'badge-secondary' }}" style="font-size: 0.75rem;">
                                {{ str_replace('_', ' ', $app->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.admin.pengajuan_nikah.show', $app->id) }}" class="btn-action btn-view" title="Lihat & Verifikasi">
                                <i class="fa-solid fa-folder-open"></i>
                            </a>
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
