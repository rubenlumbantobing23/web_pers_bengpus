@extends('layouts.user')

@section('page-title', 'Detail Pengajuan Nikah')

@section('user-content')
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
</style>

<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Top Info Bar --}}
    <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                <a href="{{ route('user.pengajuan_nikah.index') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted)'">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
            <h2 style="font-size: 1.4rem; color: #fff; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                Pengajuan Nikah #{{ $application->id }}
                — {{ $application->peran_anggota }}
            </h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px; margin-bottom: 0;">
                Diajukan: {{ $application->tanggal_pengajuan->format('d M Y') }}
                | Rencana Nikah: {{ $application->tanggal_rencana_nikah->format('d M Y') }}
            </p>
        </div>
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
            @php
                $statusConfig = [
                    'DRAFT'           => ['class' => 'badge-secondary', 'icon' => 'fa-pen'],
                    'DIAJUKAN'        => ['class' => 'badge-info',      'icon' => 'fa-paper-plane'],
                    'PENGAJUAN_DISETUJUI'=> ['class' => 'badge-info',   'icon' => 'fa-thumbs-up'],
                    'PERLU_PERBAIKAN' => ['class' => 'badge-warning',   'icon' => 'fa-triangle-exclamation'],
                    'DIVERIFIKASI'    => ['class' => 'badge-primary',   'icon' => 'fa-magnifying-glass-check'],
                    'DISETUJUI'       => ['class' => 'badge-success',   'icon' => 'fa-check-double'],
                    'SELESAI'         => ['class' => 'badge-success',   'icon' => 'fa-circle-check'],
                    'DITOLAK'         => ['class' => 'badge-danger',    'icon' => 'fa-ban'],
                ];
                $cfg = $statusConfig[$application->status] ?? ['class' => 'badge-secondary', 'icon' => 'fa-circle'];
            @endphp
            <span class="badge {{ $cfg['class'] }}" style="font-size: 1rem; padding: 10px 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <i class="fa-solid {{ $cfg['icon'] }}"></i>
                {{ str_replace('_', ' ', $application->status) }}
            </span>
            @if(in_array($application->status, ['DRAFT', 'DITOLAK']))
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('user.pengajuan_nikah.edit', $application->id) }}" class="btn-military btn-interactive" style="font-size: 0.85rem; padding: 8px 14px; background: rgba(59,130,246,0.3); border-color: rgba(59,130,246,0.5);">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Pengajuan
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if(in_array($application->status, ['PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN', 'DIVERIFIKASI', 'DISETUJUI', 'SELESAI']))
    <div style="margin-top: -8px; padding: 14px 18px; border-radius: 8px; background: rgba(255,255,255,0.03); border-left: 4px solid {{ in_array($application->status, ['PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN']) ? '#60a5fa' : ($application->status === 'DIVERIFIKASI' ? '#a78bfa' : '#34d399') }};">
        @if(in_array($application->status, ['PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN']))
            <div style="font-weight: 700; margin-bottom: 8px; color: #60a5fa; font-size: 0.95rem;"><i class="fa-solid fa-route"></i> Alur</div>
            <ol style="margin: 0; padding-left: 20px; font-size: 0.85rem; display: flex; flex-direction: column; gap: 6px; color: var(--text-muted);">
                <li><strong>Download surat pengantar</strong> dari Personalia di bawah halaman.</li>
                <li><strong>Urus dan dapatkan</strong> hasil/sertifikat dari instansi terkait.</li>
                <li><strong>Upload dokumen</strong> pada halaman Dokumen Persyaratan.</li>
                <li><strong>Tunggu verifikasi</strong> dari Staf Personalia.</li>
            </ol>
        @elseif($application->status === 'DIVERIFIKASI')
            <div style="font-weight: 700; margin-bottom: 6px; color: #a78bfa; font-size: 0.95rem;"><i class="fa-solid fa-hourglass-half"></i> Dokumen Lengkap — Menunggu Persetujuan Akhir</div>
            <div style="font-size: 0.85rem; margin: 0; color: var(--text-muted);">Semua dokumen persyaratan telah diterima. Admin sedang memproses persetujuan dan pembuatan Surat Izin Nikah final.</div>
        @else
            <div style="font-weight: 700; margin-bottom: 6px; color: #34d399; font-size: 0.95rem;"><i class="fa-solid fa-circle-check"></i> Pengajuan Disetujui</div>
            <div style="font-size: 0.85rem; margin: 0; color: var(--text-muted);">Surat Izin Nikah final tersedia untuk diunduh pada bagian Surat Izin Nikah Final di bawah halaman.</div>
        @endif
    </div>
    @endif



    {{-- Alert: PERLU_PERBAIKAN --}}
    @if($application->status === 'PERLU_PERBAIKAN')
    <div class="animated-alert" style="background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.3); padding: 14px 18px; border-radius: 10px; color: #fde68a;">
        <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Pengajuan Membutuhkan Perbaikan</div>
        @if($application->catatan_admin)
            <div style="font-size: 0.9rem;">Catatan Admin: {{ $application->catatan_admin }}</div>
        @endif
        <div style="font-size: 0.85rem; margin-top: 8px; color: #fbbf24;">Silakan unggah ulang dokumen yang ditolak (lihat di panel dokumen). Setelah upload, pengajuan akan otomatis dikembalikan ke status DIAJUKAN.</div>
    </div>
    @endif

    {{-- Alert: DITOLAK --}}
    @if($application->status === 'DITOLAK')
    <div class="animated-alert" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); padding: 14px 18px; border-radius: 10px; color: #fca5a5;">
        <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-ban"></i> Pengajuan Ditolak</div>
        @if($application->catatan_admin)
            <div style="font-size: 0.9rem;">Alasan: {{ $application->catatan_admin }}</div>
        @endif
    </div>
    @endif



    <div style="display: flex; flex-direction: column; gap: 24px; width: 100%;">

        {{-- MAIN CONTENT --}}

        {{-- Surat Permohonan Izin Nikah (Tahap 1) - FULL WIDTH --}}
        @if(in_array($application->status, ['DRAFT', 'DITOLAK', 'DIAJUKAN']))
        <div class="glass-card interactive-card" style="border-color: rgba(59,130,246,0.4); background: linear-gradient(145deg, rgba(59,130,246,0.05) 0%, rgba(59,130,246,0.1) 100%); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; font-size: 10rem; opacity: 0.03; color: #60a5fa; pointer-events: none;">
                <i class="fa-solid fa-file-contract"></i>
            </div>

            <h3 style="font-size: 1.1rem; color: #60a5fa; font-weight: 700; padding-bottom: 12px; border-bottom: 1px solid rgba(59,130,246,0.2); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-contract"></i> Tahap 1: Surat Permohonan Izin Nikah
            </h3>

            @php
                $suratReqType = $requiredAnggota->where('code', 'SURAT_PERMOHONAN_IZIN_NIKAH')->first();
                $suratDoc = $suratReqType ? $application->documents->where('marriage_document_type_id', $suratReqType->id)->first() : null;
                $isUploaded = $suratDoc && $suratDoc->file_path;

                $generatedLetter = $application->letters->where('jenis_surat', 'SURAT_PERMOHONAN_IZIN_NIKAH')->first();
                $isGenerated = $generatedLetter && \Illuminate\Support\Facades\Storage::disk('private')->exists($generatedLetter->file_generated);
            @endphp

            <ol style="margin: 0 0 16px 0; padding-left: 20px; font-size: 0.9rem; display: flex; flex-direction: column; gap: 18px; color: #cbd5e1; position: relative; z-index: 1;">
                <li>
                    <strong>Surat Permohonan dibuat otomatis</strong> berdasarkan data pengajuan Anda.
                    <div style="margin-top: 8px; display: flex; gap: 10px;">
                        @if($isGenerated)
                            <a href="{{ route('user.pengajuan_nikah.download_letter', [$application->id, $generatedLetter->id]) }}" class="btn-military btn-interactive" style="font-size: 0.8rem; padding: 8px 12px; background: #34d399; color: #000; border: none;">
                                <i class="fa-solid fa-download"></i> Download Surat
                            </a>
                        @else
                            <span style="color: #fbbf24; font-size: 0.82rem;">Surat belum tersedia. Template surat perlu dilengkapi oleh Admin.</span>
                        @endif
                    </div>
                </li>
                <li><strong>Cetak & Minta Tanda Tangan:</strong> Cetak surat tersebut, kemudian minta tanda tangan atasan/Kabag sesuai ketentuan.</li>
                <li>
                    <strong>Upload Surat yang Sudah Ditandatangani:</strong> Setelah ditandatangani, scan atau foto surat tersebut lalu upload kembali ke sistem.
                    <div style="margin-top: 10px; background: rgba(0,0,0,0.2); padding: 12px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.1);">
                        @if($suratReqType)
                            @include('user.marriage_applications._document_item', ['req' => $suratReqType, 'doc' => $suratDoc, 'pihak' => 'Anggota'])
                        @else
                            <span class="badge badge-danger">KONFIGURASI DOKUMEN SURAT_PERMOHONAN_IZIN_NIKAH TIDAK DITEMUKAN.</span>
                        @endif
                    </div>
                </li>
                <li>
                    <strong>Ajukan ke Pers:</strong> Setelah surat berhasil di-upload, Anda dapat mengajukan permohonan ke Pers.
                    <div style="margin-top: 10px;">
                        @if(in_array($application->status, ['DRAFT', 'DITOLAK']))
                            <form action="{{ route('user.pengajuan_nikah.submit', $application->id) }}" method="POST" data-confirm="Pengajuan akan dikirim ke Admin untuk diperiksa." data-confirm-title="Ajukan permohonan sekarang?" data-confirm-button="Ya, ajukan" data-confirm-icon="question">
                                @csrf
                                <button type="submit" class="btn-military btn-interactive" style="font-size: 0.9rem; padding: 10px 18px; {{ !$isUploaded ? 'opacity: 0.5; pointer-events: none; background: #475569;' : 'background: #3b82f6; border-color: #60a5fa;' }}" {!! !$isUploaded ? 'disabled title="Upload Surat Permohonan terlebih dahulu"' : '' !!}>
                                    <i class="fa-solid fa-paper-plane"></i> Ajukan ke Pers
                                </button>
                            </form>
                        @elseif($application->status === 'DIAJUKAN')
                            <span class="badge badge-info" style="padding: 8px 14px; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(59,130,246,0.2);"><i class="fa-solid fa-check"></i> Pengajuan Telah Dikirim, Menunggu Verifikasi Admin</span>
                        @endif
                    </div>
                </li>
            </ol>
        </div>
        @elseif(in_array($application->status, ['PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN', 'DIVERIFIKASI', 'DISETUJUI', 'SELESAI']))
        <div class="glass-card interactive-card" style="border-left: 4px solid #34d399;">
            @if(in_array($application->status, ['DISETUJUI', 'SELESAI']))
                <h3 style="font-weight: 700; margin-bottom: 6px; color: #34d399; font-size: 1rem;"><i class="fa-solid fa-folder-open"></i> Arsip Dokumen Persyaratan</h3>
                <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 12px; margin-top: 0;">
                    Seluruh dokumen persyaratan nikah Anda telah diarsipkan. Anda dapat melihatnya kembali kapan saja.
                </p>
                <a href="{{ route('user.pengajuan_nikah.documents', $application->id) }}" class="btn-military btn-interactive" style="background: rgba(16,185,129,0.1); color: #34d399; border: 1px solid rgba(16,185,129,0.4); font-weight: 600; padding: 6px 14px; font-size: 0.85rem; display: inline-flex; align-items: center; border-radius: 6px;">
                    Lihat Arsip Dokumen <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            @else
                <h3 style="font-weight: 700; margin-bottom: 6px; color: #34d399; font-size: 1rem;"><i class="fa-solid fa-upload"></i> Tahap 2: Upload Dokumen Persyaratan</h3>
                <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 12px; margin-top: 0;">
                    Silakan unggah dokumen persyaratan nikah dari instansi terkait untuk melanjutkan.
                </p>

                <div style="margin-bottom: 20px; margin-top: 16px;">
                    <div style="font-weight: 700; color: #34d399; font-size: 0.9rem; margin-bottom: 10px;"><i class="fa-solid fa-file-signature"></i> Surat Pengantar Personalia</div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($coverLetters as $cover)
                        @php
                            $letter = $application->letters->where('jenis_surat', $cover->code)->first();
                            $letterAvailable = $letter && \Illuminate\Support\Facades\Storage::disk('private')->exists($letter->file_generated);
                        @endphp
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: rgba(0,0,0,0.25); border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div style="font-weight: 600; color: #e2e8f0; font-size: 0.85rem; margin-bottom: 2px;">{{ $cover->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    Status: @if($letterAvailable) <span class="badge badge-success" style="padding: 2px 6px; font-size: 0.65rem;">Tersedia</span> @else <span style="color: #fbbf24;">Menunggu template</span> @endif
                                </div>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                @if($letterAvailable)
                                <a href="{{ route('user.pengajuan_nikah.download_letter', [$application->id, $letter->id]) }}" class="btn-military btn-interactive" style="font-size: 0.75rem; padding: 4px 10px; background: #34d399; color: #000; border: none; border-radius: 4px;">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('user.pengajuan_nikah.documents', $application->id) }}" class="btn-military btn-interactive" style="background: rgba(16,185,129,0.1); color: #34d399; border: 1px solid rgba(16,185,129,0.4); font-weight: 600; padding: 6px 14px; font-size: 0.85rem; display: inline-flex; align-items: center; border-radius: 6px;">
                    Lanjut Upload Dokumen <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            @endif
        </div>
        

        @endif

        {{-- SURAT-SURAT --}}
        @if(in_array($application->status, ['PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN', 'DIVERIFIKASI', 'DISETUJUI', 'SELESAI']))
        <div class="dashboard-grid">


            {{-- Surat Izin Nikah Final --}}
            @if(in_array($application->status, ['DISETUJUI', 'SELESAI']))
            <details class="glass-card interactive-card" style="cursor: pointer;">
                <summary style="font-size: 1rem; color: #34d399; font-weight: 700; display: flex; align-items: center; gap: 8px; list-style: none;">
                    <i class="fa-solid fa-stamp"></i> Surat Izin Nikah Final
                    <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.8rem;"></i>
                </summary>
                @php
                    $finalLetter = $application->letters->where('jenis_surat', 'SURAT_IZIN_NIKAH_FINAL')->first();
                    $physicalExists = $finalLetter && \Illuminate\Support\Facades\Storage::disk('private')->exists($finalLetter->file_generated);
                @endphp
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 14px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid rgba(255,255,255,0.08); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                        <div>
                            <div style="font-weight: 600; color: #e2e8f0; font-size: 0.9rem; margin-bottom: 4px;">Surat Izin Nikah Final</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                Status:
                                @if($physicalExists)
                                    <span class="badge badge-success" style="padding: 2px 6px; font-size: 0.7rem;">{{ $application->status === 'SELESAI' ? 'Selesai' : 'Surat Tersedia' }}</span>
                                @else
                                    <span style="color: #fbbf24; font-size: 0.75rem;">Surat belum tersedia. Silakan hubungi Admin Personalia.</span>
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            @if($physicalExists)
                            <a href="{{ route('user.pengajuan_nikah.download_letter', [$application->id, $finalLetter->id]) }}" class="btn-military btn-interactive" style="font-size: 0.75rem; padding: 6px 10px;">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </details>
            @endif
        </div>
        @endif



        <div class="dashboard-grid">
            {{-- Riwayat Status (Denah Alur) --}}
            <div class="glass-card interactive-card history-card" style="margin-top: 12px; margin-bottom: 8px;">
                <h3 style="font-size: 1rem; color: #34d399; font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Status (Alur Proses)
                </h3>
                <div style="position: relative; padding-left: 24px; margin-top: 16px; max-height: 400px; overflow-y: auto; padding-right: 8px;">
                    <div style="position: absolute; top: 8px; bottom: 0; left: 6px; width: 2px; background: rgba(255, 255, 255, 0.1);"></div>
                    @forelse($application->statusHistories->sortByDesc('created_at') as $history)
                    @php
                        $badgeColor = '#64748b';
                        if(in_array($history->status, ['DISETUJUI', 'SELESAI'])) $badgeColor = '#10b981';
                        elseif(in_array($history->status, ['DITOLAK', 'PERLU_PERBAIKAN'])) $badgeColor = '#f87171';
                        elseif($history->status === 'DIAJUKAN') $badgeColor = '#60a5fa';
                        elseif($history->status === 'DIVERIFIKASI') $badgeColor = '#a78bfa';
                    @endphp
                    <div style="position: relative; margin-bottom: 16px;">
                        <!-- Timeline Dot -->
                        <div style="position: absolute; left: -24px; top: 6px; width: 14px; height: 14px; border-radius: 50%; background: var(--bg-sidebar); border: 3px solid {{ $badgeColor }}; z-index: 2; box-shadow: 0 0 0 3px var(--bg-sidebar);"></div>
                        
                        <!-- Timeline Content -->
                        <div style="padding: 14px; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                            <div style="font-weight: 700; color: {{ $badgeColor }}; font-size: 0.88rem; letter-spacing: 0.5px;">{{ str_replace('_', ' ', $history->status) }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                                <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> {{ $history->created_at->format('d M Y, H:i') }} 
                                <span style="margin: 0 6px;">|</span> 
                                <i class="fa-regular fa-user" style="margin-right: 4px;"></i> <span style="color: #cbd5e1;">{{ $history->changer->name ?? 'Sistem' }}</span>
                            </div>
                            @if($history->catatan)
                            <div style="font-size: 0.82rem; color: #cbd5e1; margin-top: 10px; background: rgba(0,0,0,0.25); padding: 10px 12px; border-radius: 6px; border-left: 2px solid {{ $badgeColor }}; line-height: 1.4;">
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

    </div>
</div>
@endsection
