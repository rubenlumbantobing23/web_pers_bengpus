@extends('layouts.user')

@section('page-title', 'Detail Pengajuan Izin Nikah')

@section('user-content')
<div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Detail Pengajuan Nikah {{ $marriageRequest->request_number }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Diajukan pada {{ $marriageRequest->created_at->format('d F Y H:i') }} WIB</p>
        </div>
        <a href="{{ route('user.marriage.index') }}" class="btn-secondary">
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
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">STATUS PERMOHONAN IZIN NIKAH:</span>
            <div style="margin-top: 4px;">
                @if($marriageRequest->status === 'pending')
                    <span class="badge badge-pending" style="font-size: 1rem; padding: 8px 16px;">
                        <i class="fa-solid fa-clock"></i> MENUNGGU VERIFIKASI DOKUMEN OLEH STAF PERSONALIA
                    </span>
                @elseif($marriageRequest->status === 'approved')
                    <span class="badge badge-approved" style="font-size: 1rem; padding: 8px 16px;">
                        <i class="fa-solid fa-circle-check"></i> TELAH DISETUJU & SURAT IZIN NIKAH DITERBITKAN
                    </span>
                @elseif($marriageRequest->status === 'rejected')
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

        @if($marriageRequest->status === 'rejected' && $marriageRequest->rejection_reason)
            <div style="margin-top: 20px; padding: 16px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px;">
                <strong style="color: #f87171; display: block; margin-bottom: 4px;">Catatan / Alasan Penolakan Staf Personalia:</strong>
                <p style="color: #fca5a5; font-size: 0.925rem;">{{ $marriageRequest->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <!-- Request Details Grid -->
    <div class="glass-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            Informasi Calon Pasangan & Pelaksanaan
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Nama Calon Pasangan</span>
                <div style="font-size: 1.1rem; font-weight: 700; color: #fbbf24;">{{ $marriageRequest->spouse_name }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Pekerjaan / Instansi</span>
                <div style="font-size: 1rem; font-weight: 600; color: #fff;">{{ $marriageRequest->spouse_occupation ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">NRP / NIP Pasangan</span>
                <div style="font-size: 1rem; font-weight: 600; color: #fff;">{{ $marriageRequest->spouse_nrp_nip ?? 'Swasta / Non-NIP' }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Tanggal Pernikahan</span>
                <div style="font-size: 1rem; font-weight: 600; color: #34d399;">{{ $marriageRequest->marriage_date->format('d F Y') }}</div>
            </div>

            <div style="grid-column: span 2;">
                <span style="font-size: 0.8rem; color: var(--text-muted);">Lokasi Akad / Resepsi</span>
                <div style="font-size: 0.95rem; color: #fff; font-weight: 600; margin-top: 2px;">{{ $marriageRequest->marriage_location }}</div>
            </div>
        </div>

        <h4 style="font-size: 1rem; color: #fff; margin-bottom: 12px;">Dokumen Persyaratan Terlampir:</h4>
        @if($marriageRequest->documents->isEmpty())
            <p style="color: var(--text-muted); font-size: 0.875rem;">Tidak ada dokumen terlampir.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($marriageRequest->documents as $doc)
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
