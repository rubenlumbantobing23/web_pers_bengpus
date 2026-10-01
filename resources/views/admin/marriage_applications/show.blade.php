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

<style>
    .masonry-layout {
        display: flex;
        flex-direction: column;
        gap: 24px;
        align-items: stretch;
    }
    .masonry-col {
        display: contents;
    }
    .member-card { order: 1; }
    .partner-card { order: 2; }
    .plan-card { order: 3; }
    .workflow-stage { order: 4; }
    .workflow-status { order: 5; }
    .archive-card { order: 6; }
    .cover-card { order: 7; }
    .final-card { order: 8; }
    .history-card { order: 9; }
    .detail-card-title {
        font-size: 1.15rem;
        color: var(--accent-gold);
        font-weight: 700;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="{{ route('admin.admin.pengajuan_nikah.index') }}" style="font-size: 0.85rem; color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted)'">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h2 style="font-size: clamp(1.5rem, 2vw, 1.85rem); color: #fff; font-weight: 700;">
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

    {{-- MASONRY LAYOUT --}}
    <div class="masonry-layout">

        {{-- KOLOM KIRI --}}
        <div class="masonry-col">

            {{-- Data Anggota --}}
            <div class="glass-card member-card">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-user-shield"></i> Data Anggota
                </h3>
                <table style="width: 100%; font-size: 0.95rem; color: #e2e8f0; border-collapse: collapse;">
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
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                        <td style="padding: 8px 0; width: 36%; color: var(--text-muted);">{{ $lbl }}</td>
                        <td style="padding: 8px 0; font-weight: 600;">: {{ $val ?? '-' }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>

            {{-- Rencana Pernikahan --}}
            <div class="glass-card plan-card">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-calendar-day"></i> Rencana Pernikahan
                </h3>
                <table style="width: 100%; font-size: 0.95rem; color: #e2e8f0; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; width: 36%; color: var(--text-muted);">Tanggal Rencana</td><td style="padding: 8px 0; font-weight: 600;">: {{ $application->tanggal_rencana_nikah->format('d M Y') }}</td></tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Tempat</td><td style="padding: 8px 0; font-weight: 600;">: {{ $application->tempat_nikah }}</td></tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Alamat</td><td style="padding: 8px 0; font-weight: 600;">: {{ $application->alamat_nikah }}</td></tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Kelurahan</td><td style="padding: 8px 0;">: {{ $application->kelurahan_nikah }}</td></tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Kecamatan</td><td style="padding: 8px 0;">: {{ $application->kecamatan_nikah }}</td></tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Kabupaten / Kota</td><td style="padding: 8px 0;">: {{ $application->kabupaten_nikah }}</td></tr>
                    <tr><td style="padding: 8px 0; color: var(--text-muted);">Provinsi</td><td style="padding: 8px 0;">: {{ $application->provinsi_nikah }}</td></tr>
                </table>
            </div>


            {{-- Surat Pengantar --}}
            @if(in_array($application->status, ['PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN', 'DIVERIFIKASI', 'DISETUJUI', 'SELESAI']))
            <div class="glass-card cover-card">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-file-signature"></i> Surat Pengantar
                </h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($coverLetters as $cover)
                    @php
                        $letter = $application->letters->where('jenis_surat', $cover->code)->first();
                        $letterAvailable = $letter && \Illuminate\Support\Facades\Storage::disk('private')->exists($letter->file_generated);
                    @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid rgba(255,255,255,0.07);">
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">{{ $cover->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                                @if($letterAvailable)
                                    Dibuat: {{ $letter->generated_at?->format('d M Y H:i') }}
                                @else
                                    <span style="color: #fbbf24;">Menunggu template</span>
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            @if($letterAvailable)
                            <a href="{{ route('admin.admin.pengajuan_nikah.download_letter', [$application->id, $letter->id]) }}" class="btn-military" style="font-size: 0.75rem; padding: 6px 12px;" title="Download Surat">
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
            <div class="glass-card final-card">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-stamp"></i> Surat Izin Nikah Final
                </h3>
                @php
                    $finalLetter = $application->letters->where('jenis_surat', 'SURAT_IZIN_NIKAH_FINAL')->first();
                    $finalLetterAvailable = $finalLetter && \Illuminate\Support\Facades\Storage::disk('private')->exists($finalLetter->file_generated);
                @endphp
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid rgba(255,255,255,0.07);">
                    <div>
                        <div style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">Surat Izin Nikah Final</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px;">
                            @if($finalLetterAvailable)
                                Status: <span style="color: #34d399;">{{ $application->status === 'SELESAI' ? 'Selesai' : 'Tersedia' }}</span><br>
                                Dibuat: {{ $finalLetter->generated_at?->format('d M Y H:i') }}
                            @else
                                Status: <span style="color: #fbbf24;">Menunggu template</span>
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        @if($finalLetterAvailable)
                        <a href="{{ route('admin.admin.pengajuan_nikah.download_letter', [$application->id, $finalLetter->id]) }}" class="btn-military" style="font-size: 0.75rem; padding: 10px 14px;" title="Download Surat Izin Nikah Final">
                            <i class="fa-solid fa-download" style="margin-right: 6px;"></i> Download
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- KOLOM KANAN --}}
        <div class="masonry-col">

            {{-- Data Pasangan --}}
            @if($partner)
        <div class="glass-card partner-card">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-user-heart"></i> Data {{ $partner->peran }}
                </h3>
                <table style="width: 100%; font-size: 0.95rem; color: #e2e8f0; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; width: 36%; color: var(--text-muted);">Nama</td><td style="padding: 8px 0; font-weight: 600;">: {{ $partner->nama }}</td></tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Tempat, Tgl Lahir</td><td style="padding: 8px 0; font-weight: 600;">: {{ $partner->tempat_lahir }}, {{ $partner->tanggal_lahir->format('d M Y') }}</td></tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Pekerjaan</td><td style="padding: 8px 0; font-weight: 600;">: {{ $partner->pekerjaan }} ({{ $partner->status_pekerjaan }})</td></tr>
                    @if($partner->status_pekerjaan === 'ASN')
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Instansi / Jabatan</td><td style="padding: 8px 0; font-weight: 600;">: {{ $partner->instansi }} — {{ $partner->jabatan }}</td></tr>
                    @endif
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 8px 0; color: var(--text-muted);">Agama / Suku</td><td style="padding: 8px 0; font-weight: 600;">: {{ $partner->agama }} / {{ $partner->suku }}</td></tr>
                    <tr><td style="padding: 8px 0; color: var(--text-muted);">Alamat</td><td style="padding: 8px 0;">: {{ $partner->alamat }}, Kel. {{ $partner->kelurahan }}, Kec. {{ $partner->kecamatan }}, {{ $partner->kabupaten }}, {{ $partner->provinsi }}</td></tr>
                </table>

                <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06);">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">Orang Tua / Wali</div>
                    <table style="width: 100%; font-size: 0.85rem; color: #e2e8f0; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"><td style="padding: 6px 0; width: 36%; color: var(--text-muted);">Bapak/Wali</td><td style="padding: 6px 0;">: {{ $partner->bapak_nama }} | {{ $partner->bapak_agama }} | {{ $partner->bapak_pekerjaan }}</td></tr>
                        <tr><td style="padding: 6px 0; color: var(--text-muted);">Ibu</td><td style="padding: 6px 0;">: {{ $partner->ibu_nama }} | {{ $partner->ibu_agama }} | {{ $partner->ibu_pekerjaan }}</td></tr>
                    </table>
                </div>
            </div>
            @else
            <div class="glass-card partner-card" style="display: flex; align-items: center; justify-content: center; padding: 40px;">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada data pasangan.</p>
            </div>
            @endif

            {{-- Keputusan akhir hanya tersedia setelah seluruh dokumen diverifikasi. --}}
            @if(in_array($application->status, ['DIVERIFIKASI', 'DISETUJUI']))
            <div class="glass-card workflow-status">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-sliders"></i> Keputusan Akhir Pengajuan
                </h3>
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Status saat ini</label>
                    <div class="form-control" style="display: flex; align-items: center; min-height: 42px; color: {{ $sc }}; font-weight: 700; background: rgba(0,0,0,0.1); border-color: rgba(255,255,255,0.05); margin-top: 6px;">{{ str_replace('_', ' ', $application->status) }}</div>
                    <small style="display: block; margin-top: 8px; color: var(--text-muted); font-size: 0.75rem; line-height: 1.4;">Status tahap awal dan hasil pemeriksaan dokumen mengikuti aksi verifikasi. Keputusan akhir tersedia setelah semua dokumen diterima.</small>
                </div>
                @php
                    $adminDecision = match ($application->status) {
                        'DIVERIFIKASI' => ['DISETUJUI' => 'Setujui & Buat Surat Izin Nikah'],
                        'DISETUJUI' => ['SELESAI' => 'Tandai Selesai'],
                        default => [],
                    };
                    $decisionButtonLabel = $application->status === 'DIVERIFIKASI'
                        ? 'Setujui & Buat Surat Izin Nikah'
                        : 'Tandai Pengajuan Selesai';
                @endphp
                <form action="{{ route('admin.admin.pengajuan_nikah.update_status', $application->id) }}" method="POST" data-confirm="Pengajuan akan disetujui dan Surat Izin Nikah final dibuat untuk anggota." data-confirm-title="Setujui dan buat surat?" data-confirm-button="Ya, setujui & buat surat" data-confirm-icon="question">
                        @csrf
                        <div style="display: grid; grid-template-columns: 1fr; gap: 12px; margin-bottom: 16px;">
                            <div class="form-group">
                                <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1;">Tindakan</label>
                                <select name="status" class="form-control" required style="margin-top: 6px;">
                                    <option value="">-- Pilih Keputusan --</option>
                                    @foreach($adminDecision as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1;">Catatan (opsional)</label>
                                <input type="text" name="catatan" class="form-control" style="margin-top: 6px;" value="{{ $application->catatan_admin }}" placeholder="Tambahkan catatan untuk anggota..." {{ $application->status === 'DIAJUKAN' ? 'required' : '' }}>
                            </div>
                        </div>
                        <button type="submit" class="btn-military" style="font-size: 0.85rem; padding: 10px 18px; width: 100%; justify-content: center;">
                            <i class="fa-solid fa-check"></i> {{ $decisionButtonLabel }}
                        </button>
                </form>
            </div>
            @endif

            {{-- Tahap 1: Surat Permohonan --}}
            <div class="glass-card workflow-stage">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-file-contract"></i> Tahap 1: Surat Permohonan Izin Nikah
                </h3>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    @php
                        $dtSuratPermohonan = $requiredAnggota->firstWhere('code', 'SURAT_PERMOHONAN_IZIN_NIKAH');
                        $docSuratPermohonan = $dtSuratPermohonan ? $application->documents->where('marriage_document_type_id', $dtSuratPermohonan->id)->first() : null;
                        $generatedStage1Letter = $application->letters->firstWhere('jenis_surat', 'SURAT_PERMOHONAN_IZIN_NIKAH');
                        $generatedStage1Available = $generatedStage1Letter && \Illuminate\Support\Facades\Storage::disk('private')->exists($generatedStage1Letter->file_generated);
                    @endphp
                    <div style="padding: 14px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.24);">
                        <div style="font-size: 0.84rem; color: #bfdbfe; font-weight: 700; margin-bottom: 5px;">
                            <i class="fa-solid fa-file-circle-plus"></i> Surat untuk dicetak — dibuat oleh sistem
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 10px;">Berkas awal yang diunduh anggota untuk dicetak dan ditandatangani.</div>
                        @if($generatedStage1Available)
                        <a href="{{ route('admin.admin.pengajuan_nikah.download_letter', [$application->id, $generatedStage1Letter->id]) }}" class="btn-military" style="font-size: 0.75rem; padding: 6px 12px;" title="Download surat yang dibuat sistem">
                            <i class="fa-solid fa-download"></i> Download surat awal
                        </a>
                        @else
                        <span style="font-size: 0.78rem; color: #fbbf24;">Berkas surat awal belum tersedia.</span>
                        @endif
                    </div>

                    <div style="font-size: 0.84rem; color: #cbd5e1; font-weight: 700; margin: 10px 0 2px;">
                        <i class="fa-solid fa-file-arrow-up"></i> Surat bertanda tangan — diunggah anggota untuk diperiksa
                    </div>
                    @if($dtSuratPermohonan)
                        @if($docSuratPermohonan)
                            @include('admin.marriage_applications._admin_document_item', ['doc' => $docSuratPermohonan, 'application' => $application])
                        @else
                            <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 8px; padding: 16px 14px; text-align: center; color: var(--text-muted); opacity: 0.8;">
                                <div style="font-size: 0.85rem; font-weight: 600; color: #94a3b8; margin-bottom: 8px;">{{ $dtSuratPermohonan->name }}</div>
                                <span class="badge badge-secondary" style="font-size: 0.7rem; background: rgba(255,255,255,0.1); color: #94a3b8; padding: 4px 8px;"><i class="fa-solid fa-circle-exclamation"></i> BELUM ADA DOKUMEN</span>
                            </div>
                        @endif
                    @endif
                </div>

                @if($docSuratPermohonan && $docSuratPermohonan->status_verifikasi === 'DITERIMA')
                <div style="margin-top: 20px; padding: 16px; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); border-radius: 8px; text-align: center;">
                    <div style="color: #34d399; font-weight: bold; margin-bottom: 12px;">
                        <i class="fa-solid fa-check-circle"></i> Surat Permohonan Telah Disetujui
                    </div>
                    <p style="color: #cbd5e1; font-size: 0.82rem; margin: 0 0 12px;">Status pengajuan telah diperbarui otomatis. Lanjutkan pemeriksaan dokumen persyaratan Tahap 2.</p>
                    <a href="{{ route('admin.admin.pengajuan_nikah.documents', $application->id) }}" class="btn-military" style="width: 100%; justify-content: center; text-align: center; font-size: 0.85rem; padding: 10px; background: #3b82f6; border-color: #2563eb;">
                        Periksa Dokumen Tahap 2 <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i>
                    </a>
                </div>
                @elseif($docSuratPermohonan && $docSuratPermohonan->status_verifikasi === 'DITOLAK')
                <div style="margin-top: 16px; padding: 12px 14px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.28); border-radius: 8px; color: #fcd34d; font-size: 0.82rem; line-height: 1.5;">
                    <i class="fa-solid fa-rotate"></i> Status pengajuan otomatis menjadi <strong>PERLU PERBAIKAN</strong>. Anggota dapat memperbarui surat dan mengajukannya kembali.
                </div>
                @endif
            </div>

            {{-- Riwayat Status --}}
            <div class="glass-card history-card">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Status
                </h3>
                <div style="display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto; padding-right: 8px;">
                    @forelse($application->statusHistories->sortByDesc('created_at') as $history)
                    <div style="padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.03); border-left: 3px solid {{ $statusColors[$history->status] ?? '#64748b' }};">
                        <div style="font-weight: 700; color: #fff; font-size: 0.88rem;">{{ str_replace('_', ' ', $history->status) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">{{ $history->created_at->format('d M Y, H:i') }} — {{ $history->changer->name ?? 'Sistem' }}</div>
                        @if($history->catatan)
                        <div style="font-size: 0.82rem; color: #cbd5e1; margin-top: 8px; background: rgba(0,0,0,0.2); padding: 8px 10px; border-radius: 6px;">{{ $history->catatan }}</div>
                        @endif
                    </div>
                    @empty
                    <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
