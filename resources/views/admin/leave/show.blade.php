@extends('layouts.admin')

@section('page-title', 'Verifikasi Pengajuan Cuti')

@section('admin-content')
<div style="max-width: 850px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Verifikasi Cuti: {{ $leaveRequest->request_number }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Diajukan oleh <strong style="color: #fff;">{{ $leaveRequest->user->name }}</strong> pada {{ $leaveRequest->created_at->format('d F Y H:i') }} WIB</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($leaveRequest->status === 'approved' && \App\Models\LeaveOfficialLetter::where('leave_request_id', $leaveRequest->id)->exists())
            <a href="{{ route('admin.leave.download_surat', $leaveRequest->id) }}" class="btn-military" style="background: var(--primary); border: none;">
                <i class="fa-solid fa-download"></i> Unduh Surat Cuti Resmi
            </a>
            @endif
            <a href="{{ route('admin.leave.index') }}" class="btn-secondary">
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
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">STATUS PERMOHONAN SAAT INI:</span>
                <div style="margin-top: 4px;">
                    @if($leaveRequest->status === 'pending')
                        <span class="badge badge-pending" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-clock"></i> MENUNGGU KEPUTUSAN VERIFIKASI STAF PERSONALIA
                        </span>
                    @elseif($leaveRequest->status === 'approved')
                        <span class="badge badge-approved" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-circle-check"></i> DISETUJI OLEH {{ $leaveRequest->approvedBy->name ?? 'ADMIN' }}
                        </span>
                    @elseif($leaveRequest->status === 'rejected')
                        <span class="badge badge-rejected" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-circle-xmark"></i> PENGAJUAN DITOLAK
                        </span>
                    @else
                        <span class="badge badge-cancelled" style="font-size: 1rem; padding: 8px 16px;">
                            <i class="fa-solid fa-ban"></i> PENGAJUAN DIBATALKAN ANGGOTA
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($leaveRequest->status === 'rejected' && $leaveRequest->rejection_reason)
            <div style="margin-top: 16px; padding: 12px 16px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px;">
                <strong style="color: #f87171;">Alasan Penolakan:</strong>
                <p style="color: #fca5a5; font-size: 0.9rem; margin-top: 2px;">{{ $leaveRequest->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Left: Personel Information -->
        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                Data Personel Pemohon
            </h3>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Nama Lengkap:</span>
                    <div style="font-size: 1rem; font-weight: 700; color: #fff;">{{ $leaveRequest->user->name }}</div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">NRP / NIP:</span>
                    <div style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 600;">{{ $leaveRequest->user->personel->nrp_nip ?? '-' }}</div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Pangkat / Golongan:</span>
                    <div style="font-size: 0.95rem; color: #fff;">{{ $leaveRequest->user->personel->pangkat_golongan ?? '-' }}</div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Jabatan & Satuan:</span>
                    <div style="font-size: 0.95rem; color: #fff;">{{ $leaveRequest->user->personel->jabatan ?? '-' }} ({{ $leaveRequest->user->personel->satuan_bagian ?? 'Bengpuskomlekad' }})</div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Nomor HP / WhatsApp:</span>
                    <div style="font-size: 0.95rem; color: #fff;">{{ $leaveRequest->user->personel->no_hp ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Right: Leave Parameters -->
        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                Parameter Cuti Diajukan
            </h3>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Jenis Cuti:</span>
                    <div style="font-size: 1rem; font-weight: 700; color: #fff;">{{ $leaveRequest->leaveType->name }}</div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Jumlah Hari Kerja Dihitung:</span>
                    <div style="font-size: 1.1rem; font-weight: 800; color: #34d399;">{{ $leaveRequest->working_days_count }} Hari Kerja</div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Periode Tanggal:</span>
                    <div style="font-size: 0.95rem; color: #fff; font-weight: 600;">
                        {{ $leaveRequest->start_date->format('d M Y') }} s/d {{ $leaveRequest->end_date->format('d M Y') }}
                    </div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Alasan Cuti:</span>
                    <div style="font-size: 0.875rem; color: var(--text-sub); background: rgba(9, 13, 24, 0.6); padding: 10px; border-radius: 6px; margin-top: 2px;">
                        {{ $leaveRequest->reason }}
                    </div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Tujuan / Alamat Cuti:</span>
                    <div style="font-size: 0.875rem; color: var(--text-sub); background: rgba(9, 13, 24, 0.6); padding: 10px; border-radius: 6px; margin-top: 2px;">
                        {{ $leaveRequest->tujuan ?? '-' }}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">Pengikut:</span>
                        <div style="font-size: 0.9rem; color: #fff; font-weight: 600;">{{ $leaveRequest->pengikut ?? '-' }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.775rem; color: var(--text-muted);">Kendaraan:</span>
                        <div style="font-size: 0.9rem; color: #fff; font-weight: 600;">{{ $leaveRequest->kendaraan ?? '-' }}</div>
                    </div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Kodim / Koramil:</span>
                    <div style="font-size: 0.9rem; color: #fff; font-weight: 600;">{{ $leaveRequest->kodim_koramil ?? '-' }}</div>
                </div>

                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Kontak Darurat:</span>
                    <div style="font-size: 0.9rem; color: #fff; font-weight: 600;">{{ $leaveRequest->emergency_contact }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploaded Documents Card -->
    <div class="glass-card">
        <h4 style="font-size: 1rem; color: #fff; margin-bottom: 12px;">Dokumen Lampiran Pengajuan:</h4>
        @if($leaveRequest->documents->isEmpty())
            <p style="color: var(--text-muted); font-size: 0.875rem;">Pemohon tidak melampirkan berkas fisik tambahan.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($leaveRequest->documents as $doc)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: rgba(255, 255, 255, 0.03); border-radius: 8px;">
                        <span style="font-size: 0.9rem; color: #fff;"><i class="fa-solid fa-file-pdf" style="color: var(--accent-red); margin-right: 8px;"></i> {{ $doc->document_name }}</span>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn-secondary" style="padding: 6px 14px; font-size: 0.825rem;">
                            <i class="fa-solid fa-download"></i> Unduh Berkas
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Verification Actions Form Card -->
    <div class="glass-card" style="border-color: rgba(245, 158, 11, 0.4); background: rgba(19, 27, 46, 0.95);">
        <h3 style="font-size: 1.15rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-gavel" style="color: var(--accent-gold);"></i> Form Keputusan Verifikasi Staf Personalia
        </h3>

        <form action="{{ route('admin.leave.update_status', $leaveRequest->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Tindakan Persetujuan / Penolakan</label>
                <div style="display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #34d399; cursor: pointer; padding: 10px 16px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px;">
                        <input type="radio" name="status" value="approved" id="radio_approved" onchange="onStatusChange()" {{ $leaveRequest->status === 'approved' ? 'checked' : '' }} required>
                        <i class="fa-solid fa-check-circle"></i> SETUJUI PENGAJUAN CUTI
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #f87171; cursor: pointer; padding: 10px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px;">
                        <input type="radio" name="status" value="rejected" id="radio_rejected" onchange="onStatusChange()" {{ $leaveRequest->status === 'rejected' ? 'checked' : '' }} required>
                        <i class="fa-solid fa-times-circle"></i> TOLAK PENGAJUAN CUTI
                    </label>
                </div>
            </div>

            {{-- Box: Pengaturan Hari Disetujui Kabengpus (hanya saat SETUJUI) --}}
            <div id="approved_box" style="display: {{ $leaveRequest->status === 'approved' ? 'block' : 'none' }};">
                <div style="background: rgba(16, 185, 129, 0.07); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 18px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                        <i class="fa-solid fa-stamp" style="color: #34d399; font-size: 1.2rem;"></i>
                        <strong style="color: #fff; font-size: 1rem;">Keputusan Kepala Bengpuskomlekad</strong>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="approved_days" style="color: #34d399;">
                                <i class="fa-solid fa-calendar-check"></i> Jumlah Hari Cuti Disetujui Kabengpus
                            </label>
                            <input type="number" id="approved_days" name="approved_days"
                                class="form-control"
                                value="{{ old('approved_days', $leaveRequest->approved_days ?? $leaveRequest->working_days_count) }}"
                                min="1" max="{{ $leaveRequest->working_days_count }}"
                                placeholder="{{ $leaveRequest->working_days_count }}"
                                style="font-size: 1.1rem; font-weight: 700; color: #34d399;">
                            <small style="color: var(--text-muted); display: block; margin-top: 5px;">
                                Hari diajukan: <strong style="color: #fff;">{{ $leaveRequest->working_days_count }} Hari Kerja</strong>.
                                Kosongkan = sama dengan pengajuan.
                            </small>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="approved_notes" style="color: #60a5fa;">
                                <i class="fa-solid fa-note-sticky"></i> Catatan Kabengpus (Opsional)
                            </label>
                            <input type="text" id="approved_notes" name="approved_notes"
                                class="form-control"
                                value="{{ old('approved_notes', $leaveRequest->approved_notes) }}"
                                placeholder="Misal: Disetujui sebagian karena kebutuhan dinas...">
                        </div>
                    </div>

                    @php
                        $kategori = $leaveRequest->user->personel ? $leaveRequest->user->personel->kategori_personel : '';
                        $isPerwira = in_array($kategori, ['Perwira Menengah', 'Perwira Pertama']);
                    @endphp
                    @if($isPerwira)
                    <div style="margin-top: 16px;">
                        <label class="form-label" style="color: #34d399;">
                            <i class="fa-solid fa-signature"></i> Pejabat Penandatangan Surat (Wajib)
                        </label>
                        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px 16px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff;">
                                <input type="radio" name="signing_option" value="kabeng" {{ old('signing_option') === 'kabeng' ? 'checked' : '' }}>
                                Kabeng
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px 16px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff;">
                                <input type="radio" name="signing_option" value="waka_an_kabeng" {{ old('signing_option') === 'waka_an_kabeng' ? 'checked' : '' }}>
                                Wakabeng a.n. Kabeng
                            </label>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="signing_option" value="wakabeng">
                    @endif
                </div>
            </div>

            {{-- Box: Alasan Penolakan --}}
            <div class="form-group" id="rejection_box" style="display: {{ $leaveRequest->status === 'rejected' ? 'block' : 'none' }};">
                <label class="form-label" for="rejection_reason" style="color: #f87171;">Alasan Penolakan (Wajib Diisi Jika Ditolak)</label>
                <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="3" placeholder="Tuliskan catatan perbaikan atau alasan penolakan secara jelas...">{{ old('rejection_reason', $leaveRequest->rejection_reason) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" class="btn-military" style="padding: 14px 32px;">
                    <i class="fa-solid fa-floppy-disk"></i> SIMPAN KEPUTUSAN VERIFIKASI
                </button>
            </div>
        </form>
    </div>

    @if($leaveRequest->status === 'approved')
    @php
        $isIssued = \App\Models\LeaveOfficialLetter::where('leave_request_id', $leaveRequest->id)->exists();
    @endphp
    
    <div class="glass-card" style="border-color: rgba(16, 185, 129, 0.4); background: rgba(19, 27, 46, 0.95); margin-top: 24px;">
        <h3 style="font-size: 1.15rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-file-signature" style="color: #10b981;"></i> Penerbitan Surat Cuti Resmi
        </h3>

        @if($isIssued)
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 16px; text-align: center;">
                <p style="color: #34d399; font-weight: 600; margin-bottom: 12px;">Surat Cuti Resmi sudah diterbitkan untuk pengajuan ini.</p>
                <a href="{{ route('admin.leave.download_surat', $leaveRequest->id) }}" class="btn-military" style="background: #10b981; border: none; padding: 10px 24px;">
                    <i class="fa-solid fa-download"></i> Unduh Surat Cuti Resmi
                </a>
            </div>
        @else
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; padding: 16px; text-align: center;">
                <p style="color: #fbbf24; font-weight: 600; margin-bottom: 12px;">Surat Cuti Resmi gagal diterbitkan secara otomatis.</p>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Silakan periksa konfigurasi struktur organisasi atau hubungi administrator.</p>
            </div>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script>
    function onStatusChange() {
        const isApproved = document.getElementById('radio_approved').checked;
        const isRejected = document.getElementById('radio_rejected').checked;
        document.getElementById('approved_box').style.display  = isApproved ? 'block' : 'none';
        document.getElementById('rejection_box').style.display = isRejected ? 'block' : 'none';
        if (isRejected) {
            document.getElementById('rejection_reason').focus();
        }
    }
</script>
@endpush
@endsection
