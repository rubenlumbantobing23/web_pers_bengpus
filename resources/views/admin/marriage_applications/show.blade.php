@extends('layouts.admin')

@section('page-title', 'Detail Pengajuan Nikah')

@section('admin-content')
@php
$statusColors = [
    'DRAFT'=>'#94a3b8','DIAJUKAN'=>'#60a5fa','PERLU_PERBAIKAN'=>'#fbbf24',
    'DIVERIFIKASI'=>'#a78bfa','DISETUJUI'=>'#34d399','SELESAI'=>'#10b981','DITOLAK'=>'#f87171'
];
$sc = $statusColors[$application->status] ?? '#94a3b8';
$p = $application->personel;
$partner = $application->partner;
@endphp
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="{{ route('admin.admin.pengajuan_nikah.index') }}" style="font-size: 0.85rem; color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h2 style="font-size: 1.3rem; color: #fff; font-weight: 700;">
                Pengajuan Nikah #{{ $application->id }} — {{ $p->nama ?? $application->user->name }}
            </h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                Diajukan: {{ $application->tanggal_pengajuan->format('d M Y') }} | Peran: {{ $application->peran_anggota }}
            </div>
        </div>
        <span class="badge" style="font-size: 1rem; padding: 10px 20px; background: rgba(255,255,255,0.05); border: 1px solid {{ $sc }}; color: {{ $sc }};">
            {{ str_replace('_', ' ', $application->status) }}
        </span>
    </div>

    <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 24px; align-items: start;">

        {{-- LEFT --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- Data Anggota --}}
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-user-shield"></i> Data Anggota
                </h3>
                <table style="width: 100%; font-size: 0.88rem; color: #e2e8f0; border-collapse: collapse;">
                    @foreach([
                        'Nama'              => $p->nama,
                        'NRP / NIP'         => $p->nrp_nip,
                        'Pangkat / Gol.'    => $p->pangkat_golongan,
                        'Jabatan'           => $p->jabatan,
                        'Satuan / Bagian'   => $p->satuan_bagian,
                        'Corps'             => $p->corps ?: '-',
                        'Jenis Kelamin'     => $application->jenis_kelamin_anggota,
                        'Peran'             => $application->peran_anggota,
                    ] as $lbl => $val)
                    <tr>
                        <td style="padding: 6px 0; width: 36%; color: var(--text-muted);">{{ $lbl }}</td>
                        <td style="padding: 6px 0; font-weight: 600;">: {{ $val ?? '-' }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>

            {{-- Data Pernikahan --}}
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-calendar-day"></i> Rencana Pernikahan
                </h3>
                <table style="width: 100%; font-size: 0.88rem; color: #e2e8f0; border-collapse: collapse;">
                    <tr><td style="padding: 6px 0; width: 36%; color: var(--text-muted);">Tanggal Rencana</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->tanggal_rencana_nikah->format('d M Y') }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Tempat</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->tempat_nikah }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Alamat</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->alamat_nikah }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Kelurahan</td><td style="padding: 6px 0;">: {{ $application->kelurahan_nikah }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Kecamatan</td><td style="padding: 6px 0;">: {{ $application->kecamatan_nikah }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Kabupaten / Kota</td><td style="padding: 6px 0;">: {{ $application->kabupaten_nikah }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Provinsi</td><td style="padding: 6px 0;">: {{ $application->provinsi_nikah }}</td></tr>
                </table>
            </div>

            {{-- Data Pasangan --}}
            @if($partner)
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-user-heart"></i> Data {{ $partner->peran }}
                </h3>
                <table style="width: 100%; font-size: 0.88rem; color: #e2e8f0; border-collapse: collapse;">
                    <tr><td style="padding: 5px 0; width: 36%; color: var(--text-muted);">Nama</td><td style="padding: 5px 0; font-weight: 600;">: {{ $partner->nama }}</td></tr>
                    <tr><td style="padding: 5px 0; color: var(--text-muted);">Tempat, Tgl Lahir</td><td style="padding: 5px 0; font-weight: 600;">: {{ $partner->tempat_lahir }}, {{ $partner->tanggal_lahir->format('d M Y') }}</td></tr>
                    <tr><td style="padding: 5px 0; color: var(--text-muted);">Pekerjaan</td><td style="padding: 5px 0; font-weight: 600;">: {{ $partner->pekerjaan }} ({{ $partner->status_pekerjaan }})</td></tr>
                    @if($partner->status_pekerjaan === 'ASN')
                    <tr><td style="padding: 5px 0; color: var(--text-muted);">Instansi / Jabatan</td><td style="padding: 5px 0; font-weight: 600;">: {{ $partner->instansi }} — {{ $partner->jabatan }}</td></tr>
                    @endif
                    <tr><td style="padding: 5px 0; color: var(--text-muted);">Agama / Suku</td><td style="padding: 5px 0; font-weight: 600;">: {{ $partner->agama }} / {{ $partner->suku }}</td></tr>
                    <tr><td style="padding: 5px 0; color: var(--text-muted);">Alamat</td><td style="padding: 5px 0;">: {{ $partner->alamat }}, Kel. {{ $partner->kelurahan }}, Kec. {{ $partner->kecamatan }}, {{ $partner->kabupaten }}, {{ $partner->provinsi }}</td></tr>
                </table>

                <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06);">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Orang Tua / Wali</div>
                    <table style="width: 100%; font-size: 0.85rem; color: #e2e8f0; border-collapse: collapse;">
                        <tr><td style="padding: 4px 0; width: 36%; color: var(--text-muted);">Bapak/Wali</td><td style="padding: 4px 0;">: {{ $partner->bapak_nama }} | {{ $partner->bapak_agama }} | {{ $partner->bapak_pekerjaan }}</td></tr>
                        <tr><td style="padding: 4px 0; color: var(--text-muted);">Ibu</td><td style="padding: 4px 0;">: {{ $partner->ibu_nama }} | {{ $partner->ibu_agama }} | {{ $partner->ibu_pekerjaan }}</td></tr>
                    </table>
                </div>
            </div>
            @endif

            {{-- Update Status --}}
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-sliders"></i> Ubah Status Pengajuan
                </h3>
                <form action="{{ route('admin.admin.pengajuan_nikah.update_status', $application->id) }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="form-group">
                            <label style="font-size: 0.8rem;">Status Baru</label>
                            <select name="status" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                @foreach(['DIVERIFIKASI', 'DISETUJUI', 'DITOLAK', 'SELESAI'] as $opt)
                                    <option value="{{ $opt }}" {{ $application->status == $opt ? 'selected' : '' }}>{{ str_replace('_', ' ', $opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.8rem;">Catatan (opsional)</label>
                            <input type="text" name="catatan" class="form-control" value="{{ $application->catatan_admin }}" placeholder="Catatan untuk anggota...">
                        </div>
                    </div>
                    <button type="submit" class="btn-military" style="font-size: 0.85rem; padding: 8px 18px;" onclick="return confirm('Ubah status pengajuan ini?')">
                        <i class="fa-solid fa-check"></i> Simpan Perubahan Status
                    </button>
                </form>
            </div>

            {{-- Surat Pengantar --}}
            @if(in_array($application->status, ['PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN', 'DIVERIFIKASI', 'DISETUJUI', 'SELESAI']))
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-file-signature"></i> Surat Pengantar
                </h3>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px; padding: 8px; background: rgba(255,255,255,0.03); border-radius: 6px;">
                    <i class="fa-solid fa-circle-info"></i> DUMMY TEMPLATE — DEVELOPMENT ONLY
                </div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($coverLetters as $cover)
                    @php 
                        $letter = $application->letters->where('jenis_surat', $cover->code)->first(); 
                    @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: rgba(255,255,255,0.03); border-radius: 6px; border: 1px solid rgba(255,255,255,0.07);">
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">{{ $cover->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                @if($letter)
                                    Dibuat: {{ $letter->generated_at?->format('d M Y H:i') }}
                                @else
                                    <span style="color: #94a3b8;">Belum Dibuat</span>
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <form action="{{ route('admin.admin.pengajuan_nikah.generate_letter', $application->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="jenis_surat" value="{{ $cover->code }}">
                                <button type="submit" class="btn-military" style="font-size: 0.7rem; padding: 6px 10px; background: rgba(100,116,139,0.3); border-color: rgba(100,116,139,0.6);" onclick="return confirm('Generate surat ini?')">
                                    <i class="fa-solid fa-rotate"></i> Generate
                                </button>
                            </form>
                            @if($letter)
                            <a href="{{ route('admin.admin.pengajuan_nikah.download_letter', [$application->id, $letter->id]) }}" class="btn-action btn-view" style="padding: 6px 10px;" title="Download Surat">
                                <i class="fa-solid fa-download"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Surat Izin Nikah Final --}}
            @if(in_array($application->status, ['DISETUJUI', 'SELESAI']))
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-stamp"></i> Surat Izin Nikah Final
                </h3>
                @php 
                    $finalLetter = $application->letters->where('jenis_surat', 'SURAT_IZIN_NIKAH_FINAL')->first(); 
                @endphp
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: rgba(255,255,255,0.03); border-radius: 6px; border: 1px solid rgba(255,255,255,0.07);">
                    <div>
                        <div style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">Surat Izin Nikah Final</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                            @if($finalLetter)
                                Status: <span style="color: #34d399;">{{ $application->status === 'SELESAI' ? 'Selesai' : 'Tersedia' }}</span><br>
                                Dibuat: {{ $finalLetter->generated_at?->format('d M Y H:i') }}
                            @else
                                Status: <span style="color: #94a3b8;">Belum Dibuat</span>
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        @if(!$finalLetter && $application->status === 'DISETUJUI')
                        <form action="{{ route('admin.admin.pengajuan_nikah.generate_letter', $application->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="jenis_surat" value="SURAT_IZIN_NIKAH_FINAL">
                            <button type="submit" class="btn-military" style="font-size: 0.75rem; padding: 8px 12px;" onclick="return confirm('Generate Surat Izin Nikah Final?')">
                                <i class="fa-solid fa-rotate"></i> Generate Surat Izin Nikah
                            </button>
                        </form>
                        @endif
                        @if($finalLetter)
                        <a href="{{ route('admin.admin.pengajuan_nikah.download_letter', [$application->id, $finalLetter->id]) }}" class="btn-military" style="font-size: 0.75rem; padding: 8px 12px;" title="Download Surat Izin Nikah Final">
                            <i class="fa-solid fa-download"></i> Download Surat Izin Nikah
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Arsip Surat Tercetak --}}
            @if($application->letters->isNotEmpty())
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-file-word"></i> Arsip Surat Tercetak
                </h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($application->letters as $letter)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: rgba(255,255,255,0.03); border-radius: 6px; border: 1px solid rgba(255,255,255,0.07);">
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">{{ $letter->jenis_surat }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $letter->generated_at?->format('d M Y H:i') }} — {{ $letter->generator->name ?? 'Admin' }}</div>
                        </div>
                        <a href="{{ route('admin.admin.pengajuan_nikah.download_letter', [$application->id, $letter->id]) }}" class="btn-action btn-view" title="Download Surat">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Riwayat Status --}}
            <div class="glass-card">
                <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Status
                </h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @forelse($application->statusHistories->sortByDesc('created_at') as $history)
                    <div style="padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.02); border-left: 3px solid {{ $statusColors[$history->status] ?? '#64748b' }};">
                        <div style="font-weight: 700; color: #fff; font-size: 0.88rem;">{{ str_replace('_', ' ', $history->status) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $history->created_at->format('d M Y, H:i') }} — {{ $history->changer->name ?? 'Sistem' }}</div>
                        @if($history->catatan)
                        <div style="font-size: 0.82rem; color: #cbd5e1; margin-top: 6px;">{{ $history->catatan }}</div>
                        @endif
                    </div>
                    @empty
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Documents --}}
        <div class="glass-card" style="position: sticky; top: 90px;">
            <h3 style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <i class="fa-solid fa-folder-open"></i> Dokumen Persyaratan
            </h3>

            <div style="font-size: 0.75rem; font-weight: 700; color: #60a5fa; text-transform: uppercase; margin-bottom: 10px;">
                <i class="fa-solid fa-user"></i> Dokumen Anggota ({{ $requiredAnggota->count() }})
            </div>
            <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
                @foreach($requiredAnggota as $dt)
                    @php $doc = $application->documents->where('marriage_document_type_id', $dt->id)->first(); @endphp
                    @if($doc)
                        @include('admin.marriage_applications._admin_document_item', ['doc' => $doc, 'application' => $application])
                    @else
                        <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 6px; padding: 10px 12px; opacity: 0.7;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">{{ $dt->name }}</div>
                                <span class="badge badge-secondary" style="font-size: 0.68rem; background: rgba(255,255,255,0.1); color: #94a3b8;">BELUM ADA</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div style="font-size: 0.75rem; font-weight: 700; color: #f472b6; text-transform: uppercase; margin-bottom: 10px;">
                <i class="fa-solid fa-user-dress"></i> Dokumen {{ $partner ? $partner->peran : 'Pasangan' }} ({{ $requiredPasangan->count() }})
            </div>
            <div style="display: flex; flex-direction: column; gap: 6px;">
                @foreach($requiredPasangan as $dt)
                    @php $doc = $application->documents->where('marriage_document_type_id', $dt->id)->first(); @endphp
                    @if($doc)
                        @include('admin.marriage_applications._admin_document_item', ['doc' => $doc, 'application' => $application])
                    @else
                        <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 6px; padding: 10px 12px; opacity: 0.7;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">{{ $dt->name }}</div>
                                <span class="badge badge-secondary" style="font-size: 0.68rem; background: rgba(255,255,255,0.1); color: #94a3b8;">BELUM ADA</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
