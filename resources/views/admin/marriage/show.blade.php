@extends('layouts.admin')

@section('page-title', 'Detail Verifikasi Permohonan Nikah')

@section('admin-content')
<div style="max-width: 850px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Verifikasi Izin Nikah: {{ $marriageRequest->request_number }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Diajukan oleh <strong style="color: #fff;">{{ $marriageRequest->user->name }}</strong> pada {{ $marriageRequest->created_at->format('d F Y H:i') }} WIB</p>
        </div>
        <a href="{{ route('admin.marriage.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Status Banner Card -->
    <div class="glass-card" style="padding: 24px; border-left: 6px solid 
        @if($marriageRequest->status === 'pending') #f59e0b 
        @elseif($marriageRequest->status === 'approved') #10b981 
        @elseif($marriageRequest->status === 'rejected') #ef4444 
        @else #94a3b8 @endif;">
        
        <div>
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">STATUS PENGAJUAN NIKAH:</span>
            <div style="margin-top: 4px;">
                @if($marriageRequest->status === 'pending')
                    <span class="badge badge-pending" style="font-size: 1rem; padding: 8px 16px;">
                        <i class="fa-solid fa-clock"></i> MENUNGGU KEPUTUSAN VERIFIKASI STAF PERSONALIA
                    </span>
                @elseif($marriageRequest->status === 'approved')
                    <span class="badge badge-approved" style="font-size: 1rem; padding: 8px 16px;">
                        <i class="fa-solid fa-circle-check"></i> TELAH DISETUJU & IZIN NIKAH DITERBITKAN
                    </span>
                @elseif($marriageRequest->status === 'rejected')
                    <span class="badge badge-rejected" style="font-size: 1rem; padding: 8px 16px;">
                        <i class="fa-solid fa-circle-xmark"></i> PENGAJUAN DITOLAK
                    </span>
                @else
                    <span class="badge badge-cancelled" style="font-size: 1rem; padding: 8px 16px;">
                        <i class="fa-solid fa-ban"></i> DIBATALKAN
                    </span>
                @endif
            </div>
        </div>

        @if($marriageRequest->status === 'rejected' && $marriageRequest->rejection_reason)
            <div style="margin-top: 16px; padding: 12px 16px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px;">
                <strong style="color: #f87171;">Alasan Penolakan:</strong>
                <p style="color: #fca5a5; font-size: 0.9rem; margin-top: 2px;">{{ $marriageRequest->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                Data Personel Pemohon
            </h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Nama Pemohon:</span>
                    <div style="font-size: 1rem; font-weight: 700; color: #fff;">{{ $marriageRequest->user->name }}</div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">NRP / NIP:</span>
                    <div style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 600;">{{ $marriageRequest->user->personel->nrp_nip ?? '-' }}</div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Pangkat & Jabatan:</span>
                    <div style="font-size: 0.95rem; color: #fff;">{{ $marriageRequest->user->personel->pangkat_golongan ?? '-' }} — {{ $marriageRequest->user->personel->jabatan ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                Data Calon Pasangan
            </h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Nama Calon Pasangan:</span>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #fbbf24;">{{ $marriageRequest->spouse_name }}</div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Pekerjaan Pasangan:</span>
                    <div style="font-size: 0.95rem; color: #fff;">{{ $marriageRequest->spouse_occupation ?? 'Swasta' }}</div>
                </div>
                <div>
                    <span style="font-size: 0.775rem; color: var(--text-muted);">Rencana Pernikahan:</span>
                    <div style="font-size: 0.95rem; color: #34d399; font-weight: 600;">{{ $marriageRequest->marriage_date->format('d F Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Card -->
    <div class="glass-card">
        <h4 style="font-size: 1rem; color: #fff; margin-bottom: 12px;">Dokumen Lampiran Persyaratan:</h4>
        @if($marriageRequest->documents->isEmpty())
            <p style="color: var(--text-muted); font-size: 0.875rem;">Tidak ada file dokumen terlampir.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($marriageRequest->documents as $doc)
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

    <!-- Verification Decision Form -->
    <div class="glass-card" style="border-color: rgba(245, 158, 11, 0.4);">
        <h3 style="font-size: 1.15rem; color: #fff; margin-bottom: 16px;">Keputusan Verifikasi Izin Nikah</h3>

        <form action="{{ route('admin.marriage.update_status', $marriageRequest->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #34d399; cursor: pointer; padding: 10px 16px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px;">
                        <input type="radio" name="status" value="approved" onchange="toggleRejectionBox(false)" {{ $marriageRequest->status === 'approved' ? 'checked' : '' }} required>
                        <i class="fa-solid fa-check-circle"></i> SETUJUI PENGAJUAN NIKAH
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #f87171; cursor: pointer; padding: 10px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px;">
                        <input type="radio" name="status" value="rejected" onchange="toggleRejectionBox(true)" {{ $marriageRequest->status === 'rejected' ? 'checked' : '' }} required>
                        <i class="fa-solid fa-times-circle"></i> TOLAK PENGAJUAN NIKAH
                    </label>
                </div>
            </div>

            <div class="form-group" id="rejection_box" style="display: {{ $marriageRequest->status === 'rejected' ? 'block' : 'none' }};">
                <label class="form-label" for="rejection_reason" style="color: #f87171;">Catatan Alasan Penolakan</label>
                <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="3" placeholder="Tuliskan alasan penolakan atau berkas yang kurang...">{{ old('rejection_reason', $marriageRequest->rejection_reason) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" class="btn-military" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); padding: 14px 32px;">
                    <i class="fa-solid fa-floppy-disk"></i> SIMPAN KEPUTUSAN
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    function toggleRejectionBox(show) {
        document.getElementById('rejection_box').style.display = show ? 'block' : 'none';
    }
</script>
@endpush
@endsection
