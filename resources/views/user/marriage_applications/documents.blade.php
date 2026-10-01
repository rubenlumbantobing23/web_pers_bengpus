@extends('layouts.user')

@section('page-title', 'Upload Dokumen Persyaratan Nikah')

@section('user-content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 800px; margin: 0 auto;">
    
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
        <a href="{{ route('user.pengajuan_nikah.show', $application->id) }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pengajuan
        </a>
    </div>

    <div class="glass-card">
        <h3 style="font-size: 1.1rem; color: var(--accent-gold); font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px;">
            <i class="fa-solid fa-folder-open"></i> Dokumen Persyaratan Nikah (Tahap 2)
        </h3>

        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 14px; padding: 12px; background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3); border-radius: 8px; color: #bfdbfe;">
            <i class="fa-solid fa-circle-info"></i> Harap upload seluruh dokumen persyaratan yang diperlukan. Format file yang diizinkan adalah PDF, JPG, atau PNG dengan ukuran maksimal 5 MB per file.
        </div>

        <div style="font-size: 0.75rem; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">
            <i class="fa-solid fa-user"></i> DOKUMEN ANGGOTA ({{ count($requiredAnggota) }})
        </div>
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px;">
            @foreach($requiredAnggota as $reqType)
                @if($reqType->code !== 'SURAT_PERMOHONAN_IZIN_NIKAH')
                    @php $doc = $application->documents->where('marriage_document_type_id', $reqType->id)->first(); @endphp
                    @include('user.marriage_applications._document_item', ['req' => $reqType, 'doc' => $doc, 'pihak' => 'Anggota'])
                @endif
            @endforeach
        </div>

        <div style="font-size: 0.75rem; font-weight: 700; color: #f472b6; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">
            <i class="fa-solid fa-user-dress"></i> DOKUMEN {{ strtoupper($application->partner->peran ?? 'PASANGAN') }} ({{ count($requiredPasangan) }})
        </div>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @foreach($requiredPasangan as $reqType)
                @php $doc = $application->documents->where('marriage_document_type_id', $reqType->id)->first(); @endphp
                @include('user.marriage_applications._document_item', ['req' => $reqType, 'doc' => $doc, 'pihak' => 'Pasangan'])
            @endforeach
        </div>
    </div>
</div>
@endsection
