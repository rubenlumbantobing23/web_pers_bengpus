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
    /* Interactivity enhancements */
    .interactive-card {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.3s ease;
    }
    .interactive-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        border-color: rgba(255,255,255,0.2);
    }
    .btn-interactive {
        transition: all 0.2s ease-in-out;
    }
    .btn-interactive:hover:not([disabled]) {
        transform: translateY(-1px) scale(1.02);
        filter: brightness(1.15);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-interactive:active:not([disabled]) {
        transform: translateY(0) scale(0.98);
    }

    /* Layout grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    @media (min-width: 992px) {
        .dashboard-grid.two-cols {
            grid-template-columns: 1fr 1fr;
        }
    }

    /* Alert animation */
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animated-alert {
        animation: slideInDown 0.4s ease-out forwards;
    }

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
    .workflow-stage-2 { order: 5; }
    .workflow-status { order: 6; }
    .archive-card { order: 7; }
    .cover-card { order: 8; }
    .final-card { order: 9; }
    .history-card { order: 10; }
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
            <div class="glass-card interactive-card member-card">
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
            <div class="glass-card interactive-card plan-card">
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



        </div>

        {{-- KOLOM KANAN --}}
        <div class="masonry-col">

            {{-- Data Pasangan --}}
            @if($partner)
        <div class="glass-card interactive-card partner-card">
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
            <div class="glass-card interactive-card partner-card" style="display: flex; align-items: center; justify-content: center; padding: 40px;">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada data pasangan.</p>
            </div>
            @endif

            {{-- Tahap 3: Finalisasi --}}
            @if(in_array($application->status, ['DIVERIFIKASI', 'SELESAI']))
            <div class="glass-card interactive-card workflow-status">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-stamp"></i> Tahap 3: Finalisasi
                </h3>
                
                @if($application->status === 'SELESAI')
                    @php
                        $finalLetter = $application->letters->where('jenis_surat', 'SURAT_IZIN_NIKAH_FINAL')->first();
                        $finalLetterAvailable = $finalLetter && \Illuminate\Support\Facades\Storage::disk('private')->exists($finalLetter->file_generated);
                    @endphp
                    <div style="margin-top: 16px; padding: 12px 16px; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); border-radius: 8px; display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-check-circle" style="color: #34d399; font-size: 1.2rem;"></i>
                            <span style="color: #cbd5e1; font-size: 0.85rem;">Pengajuan selesai. Surat Izin Nikah Final telah diterbitkan.</span>
                        </div>
                        
                        @if($finalLetterAvailable)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: rgba(0,0,0,0.2); border-radius: 6px;">
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">Surat Izin Nikah Final</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Dibuat: {{ $finalLetter->generated_at?->format('d M Y H:i') }}</div>
                            </div>
                            <a href="{{ route('admin.admin.pengajuan_nikah.download_letter', [$application->id, $finalLetter->id]) }}" class="btn-military btn-interactive" style="font-size: 0.75rem; padding: 8px 12px;">
                                <i class="fa-solid fa-download" style="margin-right: 6px;"></i> Download Surat
                            </a>
                        </div>
                        @endif
                    </div>
                @else
                    <div style="margin-bottom: 16px;">
                        <p style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.5; margin-bottom: 12px;">Seluruh dokumen persyaratan telah disetujui. Silakan finalisasi pengajuan ini untuk menerbitkan Surat Izin Nikah.</p>
                    </div>
                    <form action="{{ route('admin.admin.pengajuan_nikah.update_status', $application->id) }}" method="POST" data-confirm="Pengajuan akan disetujui dan Surat Izin Nikah final dibuat untuk anggota." data-confirm-title="Setujui dan buat surat?" data-confirm-button="Ya, setujui & buat surat" data-confirm-icon="question">
                            @csrf
                            <input type="hidden" name="status" value="SELESAI">
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1;">Catatan (opsional)</label>
                                <input type="text" name="catatan" class="form-control" style="margin-top: 6px;" value="{{ $application->catatan_admin }}" placeholder="Tambahkan catatan untuk anggota...">
                            </div>
                            <button type="submit" class="btn-military btn-interactive" style="font-size: 0.85rem; padding: 10px 18px; width: 100%; justify-content: center;">
                                <i class="fa-solid fa-check"></i> Terbitkan Surat Izin Nikah Final
                            </button>
                    </form>
                @endif
            </div>
            @endif

            {{-- Tahap 1: Surat Permohonan --}}
            <div class="glass-card interactive-card workflow-stage">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-file-contract"></i> Tahap 1: Surat Permohonan Izin Nikah
                </h3>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    @php
                        $dtSuratPermohonan = $requiredAnggota->firstWhere('code', 'SURAT_PERMOHONAN_IZIN_NIKAH');
                        $docSuratPermohonan = $dtSuratPermohonan ? $application->documents->where('marriage_document_type_id', $dtSuratPermohonan->id)->first() : null;
                    @endphp
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
                <div style="margin-top: 20px; padding: 12px 16px; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-check-circle" style="color: #34d399; font-size: 1.2rem;"></i>
                    <span style="color: #cbd5e1; font-size: 0.85rem;">Surat Permohonan telah disetujui.</span>
                </div>
                @elseif($docSuratPermohonan && $docSuratPermohonan->status_verifikasi === 'DITOLAK')
                <div style="margin-top: 16px; padding: 12px 14px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.28); border-radius: 8px; color: #fcd34d; font-size: 0.82rem; line-height: 1.5;">
                    <i class="fa-solid fa-rotate"></i> Status pengajuan otomatis menjadi <strong>PERLU PERBAIKAN</strong>. Anggota dapat memperbarui surat dan mengajukannya kembali.
                </div>
                @endif
            </div>

            {{-- Tahap 2: Dokumen Persyaratan --}}
            @if($docSuratPermohonan && $docSuratPermohonan->status_verifikasi === 'DITERIMA')
            <div class="glass-card interactive-card workflow-stage-2">
                <h3 class="detail-card-title">
                    <i class="fa-solid fa-folder-open"></i> Tahap 2: Dokumen Persyaratan
                </h3>
                <p style="color: #cbd5e1; font-size: 0.85rem; margin-bottom: 16px; line-height: 1.5;">
                    Surat permohonan izin nikah telah disetujui. Lanjutkan ke pemeriksaan dokumen persyaratan untuk memvalidasi kelengkapan berkas anggota dan pasangan.
                </p>
                <a href="{{ route('admin.admin.pengajuan_nikah.documents', $application->id) }}" class="btn-military btn-interactive" style="width: 100%; justify-content: center; text-align: center; font-size: 0.85rem; padding: 12px; background: #3b82f6; border-color: #2563eb;">
                    Periksa Dokumen Tahap 2 <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            </div>
            @endif



        </div>
    </div>

    {{-- Riwayat Status (Denah Alur) --}}
    <div class="glass-card interactive-card history-card" style="margin-top: 24px; margin-bottom: 8px;">
        <h3 class="detail-card-title">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Status (Alur Proses)
        </h3>
        <div style="position: relative; padding-left: 24px; margin-top: 16px; max-height: 400px; overflow-y: auto; padding-right: 8px;">
            <div style="position: absolute; top: 8px; bottom: 0; left: 6px; width: 2px; background: rgba(255, 255, 255, 0.1);"></div>
            @forelse($application->statusHistories->sortByDesc('created_at') as $history)
            <div style="position: relative; margin-bottom: 16px;">
                <!-- Timeline Dot -->
                <div style="position: absolute; left: -24px; top: 6px; width: 14px; height: 14px; border-radius: 50%; background: var(--bg-sidebar); border: 3px solid {{ $statusColors[$history->status] ?? '#64748b' }}; z-index: 2; box-shadow: 0 0 0 3px var(--bg-sidebar);"></div>
                
                <!-- Timeline Content -->
                <div style="padding: 14px; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                    <div style="font-weight: 700; color: {{ $statusColors[$history->status] ?? '#fff' }}; font-size: 0.88rem; letter-spacing: 0.5px;">{{ str_replace('_', ' ', $history->status) }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                        <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> {{ $history->created_at->format('d M Y, H:i') }} 
                        <span style="margin: 0 6px;">|</span> 
                        <i class="fa-regular fa-user" style="margin-right: 4px;"></i> <span style="color: #cbd5e1;">{{ $history->changer->name ?? 'Sistem' }}</span>
                    </div>
                    @if($history->catatan)
                    <div style="font-size: 0.82rem; color: #cbd5e1; margin-top: 10px; background: rgba(0,0,0,0.25); padding: 10px 12px; border-radius: 6px; border-left: 2px solid {{ $statusColors[$history->status] ?? '#64748b' }}; line-height: 1.4;">
                        {{ $history->catatan }}
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">Belum ada riwayat.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
