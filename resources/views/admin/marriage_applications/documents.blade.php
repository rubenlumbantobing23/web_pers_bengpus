@extends('layouts.admin')

@section('page-title', 'Dokumen Persyaratan')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <a href="{{ route('admin.admin.pengajuan_nikah.show', $application->id) }}" style="font-size: 0.85rem; color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail
            </a>
            <h2 style="font-size: 1.3rem; color: #fff; font-weight: 700;">
                Dokumen Persyaratan — {{ $application->personel->nama ?? $application->user->name }}
            </h2>
        </div>
    </div>

    @if($application->status === 'DIVERIFIKASI')
    <div class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; border-color: rgba(167, 139, 250, 0.35); background: rgba(167, 139, 250, 0.08);">
        <div>
            <h3 style="color: #c4b5fd; font-size: 1rem; margin-bottom: 6px;"><i class="fa-solid fa-circle-check"></i> Semua dokumen wajib sudah diterima</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">Lanjutkan ke detail pengajuan untuk menyetujui permohonan dan membuat Surat Izin Nikah final.</p>
        </div>
        <a href="{{ route('admin.admin.pengajuan_nikah.show', $application->id) }}" class="btn-military" style="white-space: nowrap;">
            Setujui & Buat Surat Final <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        {{-- Dokumen Anggota --}}
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 1.1rem; color: #60a5fa; font-weight: 700; margin-bottom: 10px;">
                    <i class="fa-solid fa-user"></i> Dokumen Anggota
                </h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 15px;">
                    {{ $countAnggota }} dokumen wajib<br>
                    {{ $verifiedAnggota }} / {{ $countAnggota }} diperiksa
                </p>
                <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden; margin-bottom: 20px;">
                    <div style="width: {{ $countAnggota > 0 ? ($verifiedAnggota / $countAnggota) * 100 : 0 }}%; height: 100%; background: #60a5fa;"></div>
                </div>
            </div>
            <a href="{{ route('admin.admin.pengajuan_nikah.document_review', ['id' => $application->id, 'type' => 'anggota']) }}" class="btn-military" style="text-align: center; background: rgba(96, 165, 250, 0.1); border-color: rgba(96, 165, 250, 0.3); color: #60a5fa;">
                Periksa Dokumen Anggota <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        {{-- Dokumen Pasangan --}}
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 1.1rem; color: #f472b6; font-weight: 700; margin-bottom: 10px;">
                    <i class="fa-solid fa-user-dress"></i> Dokumen {{ $application->partner ? $application->partner->peran : 'Pasangan' }}
                </h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 15px;">
                    {{ $countPasangan }} dokumen wajib<br>
                    {{ $verifiedPasangan }} / {{ $countPasangan }} diperiksa
                </p>
                <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden; margin-bottom: 20px;">
                    <div style="width: {{ $countPasangan > 0 ? ($verifiedPasangan / $countPasangan) * 100 : 0 }}%; height: 100%; background: #f472b6;"></div>
                </div>
            </div>
            <a href="{{ route('admin.admin.pengajuan_nikah.document_review', ['id' => $application->id, 'type' => 'pasangan']) }}" class="btn-military" style="text-align: center; background: rgba(244, 114, 182, 0.1); border-color: rgba(244, 114, 182, 0.3); color: #f472b6;">
                Periksa Dokumen Pasangan <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection
