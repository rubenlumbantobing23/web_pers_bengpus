@extends('layouts.user')

@section('page-title', 'Detail Pengajuan Cuti')

@section('user-content')
<div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Detail Pengajuan {{ $leaveRequest->request_number }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Diajukan pada {{ $leaveRequest->created_at->format('d F Y H:i') }} WIB</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($leaveRequest->status === 'approved' && \App\Models\LeaveOfficialLetter::where('leave_request_id', $leaveRequest->id)->exists())
            <a href="{{ route('user.leave.download_surat', $leaveRequest->id) }}" class="btn-military" style="background: var(--primary); border: none;">
                <i class="fa-solid fa-download"></i> Unduh Surat Cuti
            </a>
            @endif
            <a href="{{ route('user.leave.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Status Banner Card -->
    <div class="glass-card" style="padding: 24px; border-left: 6px solid 
        @if($leaveRequest->status === 'pending') #f59e0b 
        @elseif($leaveRequest->status === 'approved') #10b981 
        @elseif($leaveRequest->status === 'rejected') #ef4444 
        @else #94a3b8 @endif;">
        
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">STATUS PENGAJUAN:</span>
                <div style="margin-top: 4px;">
                    @if($leaveRequest->status === 'pending')
                        <span class="badge badge-pending" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-clock"></i> MENUNGGU VERIFIKASI STAF PERSONALIA
                        </span>
                    @elseif($leaveRequest->status === 'approved')
                        <span class="badge badge-approved" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-circle-check"></i> TELAH DISETUJUI & DIVERIFIKASI
                        </span>
                    @elseif($leaveRequest->status === 'rejected')
                        <span class="badge badge-rejected" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-circle-xmark"></i> PENGAJUAN DITOLAK
                        </span>
                    @else
                        <span class="badge badge-cancelled" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-ban"></i> PENGAJUAN DIBATALKAN
                        </span>
                    @endif
                </div>
            </div>

            @if($leaveRequest->status === 'pending')
                <form action="{{ route('user.leave.cancel', $leaveRequest->id) }}" method="POST" data-confirm="Pengajuan cuti ini akan dibatalkan." data-confirm-title="Batalkan pengajuan cuti?" data-confirm-button="Ya, batalkan">
                    @csrf
                    <button type="submit" class="btn-danger">
                        <i class="fa-solid fa-ban"></i> Batalkan Pengajuan
                    </button>
                </form>
            @endif
        </div>

        @if($leaveRequest->status === 'rejected' && $leaveRequest->rejection_reason)
            <div style="margin-top: 20px; padding: 16px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px;">
                <strong style="color: #f87171; display: block; margin-bottom: 4px;">Catatan / Alasan Penolakan Staf Personalia:</strong>
                <p style="color: #fca5a5; font-size: 0.925rem;">{{ $leaveRequest->rejection_reason }}</p>
            </div>
        @endif
    </div>

    {{-- Keputusan Kabengpus (tampil jika sudah approved dan ada approved_days) --}}
    @if($leaveRequest->status === 'approved' && $leaveRequest->approved_days)
        <div class="glass-card" style="border-color: rgba(52, 211, 153, 0.5); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, rgba(52,211,153,0.3), rgba(16,185,129,0.15)); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                    <i class="fa-solid fa-stamp" style="color: #34d399;"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #34d399; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">Keputusan Kepala Bengpuskomlekad</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #fff;">Persetujuan Cuti Resmi</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div style="background: rgba(52, 211, 153, 0.07); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 10px; padding: 14px; text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">Hari Diajukan</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #60a5fa;">{{ $leaveRequest->working_days_count }} Hari</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">Hari Kerja</div>
                </div>
                <div style="background: rgba(52, 211, 153, 0.12); border: 1px solid rgba(52, 211, 153, 0.35); border-radius: 10px; padding: 14px; text-align: center;">
                    <div style="font-size: 0.75rem; color: #34d399; margin-bottom: 4px; font-weight: 700;">✓ Disetujui Kabengpus</div>
                    <div style="font-size: 1.8rem; font-weight: 900; color: #34d399;">{{ $leaveRequest->approved_days }} Hari</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">Hari Kerja Resmi</div>
                </div>
            </div>

            @if($leaveRequest->approved_days < $leaveRequest->working_days_count)
                <div style="margin-top: 14px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; padding: 12px 16px; display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b; margin-top: 2px;"></i>
                    <div>
                        <strong style="color: #fbbf24; font-size: 0.875rem;">Cuti Disetujui Sebagian</strong>
                        <p style="color: #fef08a; font-size: 0.82rem; margin-top: 3px;">
                            Kepala Bengpuskomlekad menyetujui {{ $leaveRequest->approved_days }} dari {{ $leaveRequest->working_days_count }} hari yang Anda ajukan.
                            Hubungi Staf Personalia untuk informasi lebih lanjut.
                        </p>
                    </div>
                </div>
            @endif

            @if($leaveRequest->approved_notes)
                <div style="margin-top: 12px; background: rgba(255,255,255,0.04); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px;">
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Catatan dari Kepala Bengpuskomlekad:</span>
                    <p style="color: #fff; font-size: 0.9rem; margin-top: 4px; font-style: italic;">"{{ $leaveRequest->approved_notes }}"</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Request Details Grid -->
    <div class="glass-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            Informasi Permohonan Cuti
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Jenis Cuti</span>
                <div style="font-size: 1rem; font-weight: 700; color: #fff;">{{ $leaveRequest->leaveType->name }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Jumlah Hari Kerja</span>
                <div style="font-size: 1rem; font-weight: 700; color: #34d399;">{{ $leaveRequest->working_days_count }} Hari Kerja</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Tanggal Mulai Cuti</span>
                <div style="font-size: 1rem; font-weight: 600; color: #fff;">{{ $leaveRequest->start_date->format('d F Y') }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Tanggal Selesai Cuti</span>
                <div style="font-size: 1rem; font-weight: 600; color: #fff;">{{ $leaveRequest->end_date->format('d F Y') }}</div>
            </div>

            <div style="grid-column: span 2;">
                <span style="font-size: 0.8rem; color: var(--text-muted);">Alasan / Keperluan Cuti</span>
                <div style="font-size: 0.95rem; color: var(--text-sub); margin-top: 4px; background: rgba(9, 13, 24, 0.7); padding: 12px; border-radius: 8px;">
                    {{ $leaveRequest->reason }}
                </div>
            </div>

            <div style="grid-column: span 2;">
                <span style="font-size: 0.8rem; color: var(--text-muted);">Tujuan / Alamat Cuti</span>
                <div style="font-size: 0.95rem; color: var(--text-sub); margin-top: 4px; background: rgba(9, 13, 24, 0.7); padding: 12px; border-radius: 8px;">
                    {{ $leaveRequest->tujuan ?? '-' }}
                </div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Pengikut</span>
                <div style="font-size: 0.95rem; color: #fff; font-weight: 600;">{{ $leaveRequest->pengikut ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Kendaraan</span>
                <div style="font-size: 0.95rem; color: #fff; font-weight: 600;">{{ $leaveRequest->kendaraan ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Kodim / Koramil</span>
                <div style="font-size: 0.95rem; color: #fff; font-weight: 600;">{{ $leaveRequest->kodim_koramil ?? '-' }}</div>
            </div>

            <div style="grid-column: span 2;">
                <span style="font-size: 0.8rem; color: var(--text-muted);">Kontak Darurat</span>
                <div style="font-size: 0.95rem; color: #fff; font-weight: 600;">{{ $leaveRequest->emergency_contact }}</div>
            </div>
        </div>

        <!-- Uploaded Documents List -->
        <h4 style="font-size: 1rem; color: #fff; margin-bottom: 12px;">Dokumen Lampiran:</h4>
        @if($leaveRequest->documents->isEmpty())
            <p style="color: var(--text-muted); font-size: 0.875rem;">Tidak ada dokumen yang dilampirkan.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($leaveRequest->documents as $doc)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: rgba(255, 255, 255, 0.03); border-radius: 8px;">
                        <span style="font-size: 0.875rem; color: #fff;"><i class="fa-solid fa-file-pdf" style="color: var(--accent-red); margin-right: 8px;"></i> {{ $doc->document_name }}</span>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn-secondary" style="padding: 4px 10px; font-size: 0.8rem;">
                            <i class="fa-solid fa-download"></i> Unduh File
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
