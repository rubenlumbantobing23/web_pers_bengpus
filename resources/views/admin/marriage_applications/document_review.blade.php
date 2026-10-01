@extends('layouts.admin')

@section('page-title', 'Pemeriksaan Dokumen ' . ucfirst($type))

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <a href="{{ route('admin.admin.pengajuan_nikah.documents', $application->id) }}" style="font-size: 0.85rem; color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dokumen Persyaratan
            </a>
            <h2 style="font-size: 1.3rem; color: #fff; font-weight: 700;">
                Dokumen {{ ucfirst($type) }} — {{ $application->personel->nama ?? $application->user->name }}
            </h2>
        </div>
    </div>

    @if($application->status === 'DIVERIFIKASI')
    <div class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; border-color: rgba(167, 139, 250, 0.35); background: rgba(167, 139, 250, 0.08);">
        <div>
            <h3 style="color: #c4b5fd; font-size: 1rem; margin-bottom: 6px;"><i class="fa-solid fa-circle-check"></i> Pemeriksaan seluruh dokumen selesai</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">Semua dokumen wajib telah diterima. Admin dapat menyetujui pengajuan dan membuat Surat Izin Nikah final.</p>
        </div>
        <a href="{{ route('admin.admin.pengajuan_nikah.show', $application->id) }}" class="btn-military" style="white-space: nowrap;">
            Lanjutkan Persetujuan <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    @endif

    <div class="glass-card">
        <h3 style="font-size: 0.95rem; color: {{ $type === 'anggota' ? 'var(--accent-gold)' : '#f472b6' }}; font-weight: 700; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <i class="fa-solid fa-{{ $type === 'anggota' ? 'user' : 'user-dress' }}"></i> Daftar Dokumen {{ ucfirst($type) }}
        </h3>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            @foreach($requiredDocs as $dt)
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
@endsection
