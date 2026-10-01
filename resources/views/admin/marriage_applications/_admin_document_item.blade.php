<div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 6px; padding: 10px 12px;">

    {{-- Document Name + Status --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 6px;">
        <div style="font-size: 0.8rem; color: #e2e8f0; font-weight: 600; line-height: 1.3; flex: 1;">{{ $doc->jenis_dokumen }}</div>
        @if($doc->status_verifikasi === 'DITERIMA')
            <span class="badge badge-success" style="font-size: 0.68rem;"><i class="fa-solid fa-check"></i> DITERIMA</span>
        @elseif($doc->status_verifikasi === 'DITOLAK')
            <span class="badge badge-danger" style="font-size: 0.68rem;"><i class="fa-solid fa-xmark"></i> DITOLAK</span>
        @else
            <span class="badge badge-warning" style="font-size: 0.68rem;"><i class="fa-solid fa-clock"></i> BELUM DIPERIKSA</span>
        @endif
    </div>

    {{-- File Info --}}
    <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 8px;">
        <i class="fa-solid fa-paperclip"></i> {{ $doc->file_name }}
        ({{ number_format($doc->file_size / 1024, 0) }} KB)
        | Diupload: {{ $doc->uploaded_at?->format('d M Y H:i') }}
    </div>

    {{-- Admin Catatan (if any) --}}
    @if($doc->catatan_verifikasi)
    <div style="font-size: 0.75rem; color: #fca5a5; margin-bottom: 8px; padding: 4px 8px; background: rgba(239,68,68,0.08); border-radius: 4px; border-left: 2px solid #ef4444;">
        Catatan: {{ $doc->catatan_verifikasi }}
    </div>
    @endif

    {{-- Action Buttons: Download + Verify --}}
    <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('admin.admin.pengajuan_nikah.download_document', [$application->id, $doc->id]) }}" 
           class="btn-military" style="font-size: 0.72rem; padding: 4px 10px;" title="Download dokumen">
            <i class="fa-solid fa-download"></i> Download
        </a>

        @if($doc->status_verifikasi !== 'DITERIMA')
        <form action="{{ route('admin.admin.pengajuan_nikah.verify_document', $application->id) }}" method="POST" style="display: inline-flex; gap: 4px; align-items: center;" data-confirm="Dokumen ini akan diterima sebagai dokumen yang sah." data-confirm-title="Terima dokumen?" data-confirm-button="Ya, terima" data-confirm-icon="question">
            @csrf
            <input type="hidden" name="document_id" value="{{ $doc->id }}">
            <input type="hidden" name="status" value="DITERIMA">
            <button type="submit" class="btn-military"
                    style="font-size: 0.72rem; padding: 4px 10px; background: rgba(5,150,105,0.3); border-color: rgba(5,150,105,0.5);"
                    >
                <i class="fa-solid fa-check"></i> Terima
            </button>
        </form>
        @endif

        @if($doc->status_verifikasi !== 'DITOLAK')
        <button type="button" class="btn-military" 
                style="font-size: 0.72rem; padding: 4px 10px; background: rgba(239,68,68,0.2); border-color: rgba(239,68,68,0.4);"
                onclick="document.getElementById('tolak-form-{{ $doc->id }}').style.display = document.getElementById('tolak-form-{{ $doc->id }}').style.display === 'none' ? 'block' : 'none'">
            <i class="fa-solid fa-xmark"></i> Tolak
        </button>
        @endif
    </div>

    {{-- Tolak Form (hidden by default) --}}
    @if($doc->status_verifikasi !== 'DITOLAK')
    <div id="tolak-form-{{ $doc->id }}" style="display: none; margin-top: 8px;">
        <form action="{{ route('admin.admin.pengajuan_nikah.verify_document', $application->id) }}" method="POST">
            @csrf
            <input type="hidden" name="document_id" value="{{ $doc->id }}">
            <input type="hidden" name="status" value="DITOLAK">
            <div style="display: flex; gap: 6px;">
                <input type="text" name="catatan" class="form-control" 
                       style="font-size: 0.75rem; padding: 4px 8px; flex: 1;" 
                       placeholder="Alasan penolakan (wajib)..." required>
                <button type="submit" class="btn-military" 
                        style="font-size: 0.72rem; padding: 4px 10px; background: rgba(239,68,68,0.3); border-color: rgba(239,68,68,0.5); white-space: nowrap;">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
