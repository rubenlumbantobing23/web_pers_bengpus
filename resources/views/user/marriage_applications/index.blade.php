@extends('layouts.user')

@section('page-title', 'Pengajuan Nikah Saya')

@section('user-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Top Action Bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff; font-weight: 700;">Pengajuan Nikah Saya</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">Kelola dan pantau status seluruh pengajuan nikah Anda</p>
        </div>
        <a href="{{ route('user.pengajuan_nikah.create') }}" class="btn-military">
            <i class="fa-solid fa-plus-circle"></i> Buat Pengajuan
        </a>
    </div>

    {{-- Status Summary Badges --}}
    @if($applications->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
        @php
            $statusCounts = $applications->groupBy('status')->map->count();
        @endphp
        @foreach(['DRAFT' => '#94a3b8', 'DIAJUKAN' => '#60a5fa', 'PERLU_PERBAIKAN' => '#fbbf24', 'DIVERIFIKASI' => '#a78bfa', 'DISETUJUI' => '#34d399', 'SELESAI' => '#10b981', 'DITOLAK' => '#f87171'] as $status => $color)
            @if(isset($statusCounts[$status]))
            <div class="glass-card" style="padding: 14px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: {{ $color }};">{{ $statusCounts[$status] }}</div>
                <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">{{ str_replace('_', ' ', $status) }}</div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Applications Table --}}
    <div class="glass-card">
        @if($applications->isEmpty())
            <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-ring" style="font-size: 3.5rem; margin-bottom: 20px; opacity: 0.3;"></i>
                <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 8px;">Belum Ada Pengajuan Nikah</h4>
                <p style="margin-bottom: 24px; font-size: 0.9rem;">Anda belum pernah membuat pengajuan nikah di sistem ini.</p>
                <a href="{{ route('user.pengajuan_nikah.create') }}" class="btn-military">
                    <i class="fa-solid fa-plus-circle"></i> Buat Pengajuan
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tgl Pengajuan</th>
                            <th>Rencana Nikah</th>
                            <th>Calon Pasangan</th>
                            <th>Peran Anda</th>
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
                            <td>{{ $app->tanggal_rencana_nikah->format('d M Y') }}</td>
                            <td>{{ $app->partner->nama ?? '<span style="color:var(--text-muted);font-style:italic">Belum diisi</span>' }}</td>
                            <td><span style="font-weight: 600;">{{ $app->peran_anggota }}</span></td>
                            <td>
                                @php $docCount = $app->documents()->count(); @endphp
                                <span style="font-size: 0.85rem; color: {{ $docCount > 0 ? '#34d399' : 'var(--text-muted)' }};">
                                    <i class="fa-solid fa-file-circle-{{ $docCount > 0 ? 'check' : 'xmark' }}"></i>
                                    {{ $docCount }} file
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'DRAFT'           => ['class' => 'badge-secondary', 'icon' => 'fa-pen'],
                                        'DIAJUKAN'        => ['class' => 'badge-info',      'icon' => 'fa-paper-plane'],
                                        'PERLU_PERBAIKAN' => ['class' => 'badge-warning',   'icon' => 'fa-triangle-exclamation'],
                                        'DIVERIFIKASI'    => ['class' => 'badge-primary',   'icon' => 'fa-magnifying-glass-check'],
                                        'DISETUJUI'       => ['class' => 'badge-success',   'icon' => 'fa-check-double'],
                                        'SELESAI'         => ['class' => 'badge-success',   'icon' => 'fa-circle-check'],
                                        'DITOLAK'         => ['class' => 'badge-danger',    'icon' => 'fa-ban'],
                                    ];
                                    $cfg = $statusConfig[$app->status] ?? ['class' => 'badge-secondary', 'icon' => 'fa-circle'];
                                @endphp
                                <span class="badge {{ $cfg['class'] }}">
                                    <i class="fa-solid {{ $cfg['icon'] }}"></i>
                                    {{ str_replace('_', ' ', $app->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('user.pengajuan_nikah.show', $app->id) }}" class="btn-action btn-view" title="Lihat Detail & Dokumen">
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
