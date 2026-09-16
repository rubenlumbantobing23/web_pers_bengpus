@extends('layouts.admin')

@section('page-title', 'Preview Import Data')

@section('admin-content')
<div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.4rem; color: #fff;">Preview Data Sebelum Import</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">
                Jenis: <strong style="color:{{ $jenis === 'militer' ? '#60a5fa' : '#fbbf24' }};">{{ strtoupper($jenis) }}</strong>
                &mdash; Periksa data sebelum dikonfirmasi masuk ke database.
            </p>
        </div>
        <a href="{{ route('admin.personel.import_form') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali / Batal
        </a>
    </div>

    {{-- Summary Stats --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        <div class="glass-card" style="padding:16px; display:flex; align-items:center; gap:12px;">
            <i class="fa-solid fa-circle-check" style="font-size:1.8rem; color:#34d399;"></i>
            <div>
                <div style="font-size:1.6rem; font-weight:800; color:#fff;">{{ count($preview) }}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Siap Diimport</div>
            </div>
        </div>
        <div class="glass-card" style="padding:16px; display:flex; align-items:center; gap:12px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size:1.8rem; color:#fbbf24;"></i>
            <div>
                <div style="font-size:1.6rem; font-weight:800; color:#fff;">{{ count($duplicates) }}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Duplikat (akan dilewati)</div>
            </div>
        </div>
        <div class="glass-card" style="padding:16px; display:flex; align-items:center; gap:12px;">
            <i class="fa-solid fa-database" style="font-size:1.8rem; color:#60a5fa;"></i>
            <div>
                <div style="font-size:1.6rem; font-weight:800; color:#fff;">{{ count($preview) + count($duplicates) }}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Total Baris Dibaca</div>
            </div>
        </div>
    </div>

    {{-- Data Valid --}}
    @if(count($preview) > 0)
        <div class="glass-card" style="padding:0; overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:10px;">
                <i class="fa-solid fa-circle-check" style="color:#34d399;"></i>
                <h3 style="color:#fff; font-size:1rem;">Data Valid — {{ count($preview) }} baris siap diimport</h3>
            </div>
            <div style="overflow-x:auto; max-height:400px; overflow-y:auto;">
                <table class="custom-table" style="min-width:900px; font-size:0.82rem;">
                    <thead style="position:sticky; top:0; background:var(--card-bg); z-index:10;">
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Nama</th>
                            <th>Pangkat/Gol</th>
                            <th>NRP/NIP</th>
                            <th>Corps</th>
                            <th>Jabatan</th>
                            <th>Satuan</th>
                            <th>SUKU/AGAMA</th>
                            <th>Tgl Lahir</th>
                            <th>DIKUM</th>
                            <th>{{ $jenis === 'militer' ? 'DIK PERTAMA TNI' : 'DIKLAT' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview as $i => $row)
                            <tr>
                                <td style="color:var(--text-muted);">{{ $i + 1 }}</td>
                                <td><strong style="color:#fff;">{{ $row['nama'] }}</strong></td>
                                <td style="color:var(--accent-gold);">{{ $row['pangkat_golongan'] }}</td>
                                <td><strong style="color:var(--accent-gold);">{{ $row['nrp_nip'] }}</strong></td>
                                <td>{{ $row['corps'] ?? '-' }}</td>
                                <td>{{ $row['jabatan'] }}</td>
                                <td>{{ $row['satuan_bagian'] }}</td>
                                <td>{{ $row['agama_suku'] ?? '-' }}</td>
                                <td style="white-space:nowrap;">{{ $row['tgl_lahir'] ?? '-' }}</td>
                                <td>{!! nl2br(e($row['dikum_ti'] ?: '-')) !!}</td>
                                <td style="color:#60a5fa;">{!! nl2br(e($row['dikmit_tni'] ?: '-')) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Duplikat --}}
    @if(count($duplicates) > 0)
        <div class="glass-card" style="padding:0; overflow:hidden; border-color:rgba(251,191,36,0.3);">
            <div style="padding:16px 20px; border-bottom:1px solid rgba(251,191,36,0.2); display:flex; align-items:center; gap:10px;">
                <i class="fa-solid fa-triangle-exclamation" style="color:#fbbf24;"></i>
                <h3 style="color:#fbbf24; font-size:1rem;">Data Duplikat — {{ count($duplicates) }} baris akan dilewati</h3>
            </div>
            <div style="overflow-x:auto;">
                <table class="custom-table" style="min-width:600px; font-size:0.82rem;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>NRP/NIP</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($duplicates as $i => $row)
                            <tr>
                                <td style="color:var(--text-muted);">{{ $i + 1 }}</td>
                                <td>{{ $row['nama'] }}</td>
                                <td style="color:#fbbf24;">{{ $row['nrp_nip'] }}</td>
                                <td style="color:#fbbf24; font-size:0.8rem;"><i class="fa-solid fa-circle-info"></i> NRP/NIP sudah ada di database</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if(count($preview) === 0 && count($duplicates) === 0)
        <div class="glass-card" style="text-align:center; padding:50px;">
            <i class="fa-solid fa-file-circle-exclamation" style="font-size:2.5rem; color:var(--text-muted); margin-bottom:14px;"></i>
            <p style="color:var(--text-muted);">Tidak ada data yang dapat dibaca dari file.</p>
            <a href="{{ route('admin.personel.import_form') }}" class="btn-secondary" style="margin-top:14px; display:inline-block;">
                <i class="fa-solid fa-arrow-left"></i> Kembali & Upload Ulang
            </a>
        </div>
    @else
        {{-- Confirm Buttons --}}
        <div class="glass-card" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; padding:20px 24px;">
            <div style="font-size:0.9rem; color:var(--text-muted);">
                <strong style="color:#34d399;">{{ count($preview) }} data</strong> akan ditambahkan ke database.
                @if(count($duplicates) > 0)
                    <span style="color:#fbbf24;"> {{ count($duplicates) }} duplikat</span> akan dilewati.
                @endif
            </div>
            <div style="display:flex; gap:12px;">
                <a href="{{ route('admin.personel.import_form') }}" class="btn-secondary">
                    <i class="fa-solid fa-xmark"></i> Batal
                </a>
                @if(count($preview) > 0)
                    <form action="{{ route('admin.personel.import_confirm') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-military" style="padding:12px 24px;">
                            <i class="fa-solid fa-file-import"></i> Konfirmasi & Import {{ count($preview) }} Data
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
