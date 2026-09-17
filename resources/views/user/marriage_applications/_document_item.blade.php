<div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 6px; padding: 10px 12px;">
    {{-- Title + Status Row --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 6px;">
        <div style="font-size: 0.82rem; color: #e2e8f0; font-weight: 600; line-height: 1.3; flex: 1;">{{ $req->name }}</div>
        <div style="flex-shrink: 0;">
            @if($doc)
                @if($doc->status_verifikasi === 'DITERIMA')
                    <span class="badge badge-success" style="font-size: 0.7rem;"><i class="fa-solid fa-check"></i> DITERIMA</span>
                @elseif($doc->status_verifikasi === 'DITOLAK')
                    <span class="badge badge-danger" style="font-size: 0.7rem;"><i class="fa-solid fa-xmark"></i> DITOLAK</span>
                @else
                    <span class="badge badge-secondary" style="font-size: 0.7rem;"><i class="fa-solid fa-clock"></i> DIPERIKSA</span>
                @endif
            @else
                <span class="badge badge-warning" style="font-size: 0.7rem;"><i class="fa-solid fa-triangle-exclamation"></i> BELUM ADA</span>
            @endif
        </div>
    </div>

    {{-- Admin Catatan (if rejected) --}}
    @if($doc && $doc->status_verifikasi === 'DITOLAK' && $doc->catatan_verifikasi)
        <div style="font-size: 0.78rem; color: #fca5a5; margin-bottom: 8px; padding: 5px 8px; background: rgba(239,68,68,0.1); border-radius: 4px; border-left: 2px solid #ef4444;">
            <strong>Catatan Admin:</strong> {{ $doc->catatan_verifikasi }}
        </div>
    @endif

    {{-- File Name (if uploaded) --}}
    @if($doc)
        <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <i class="fa-solid fa-paperclip"></i> {{ $doc->file_name }}
                <span style="margin-left: 8px;">({{ number_format($doc->file_size / 1024, 0) }} KB)</span>
            </div>
            <a href="{{ route('user.pengajuan_nikah.download_document', [$application->id, $doc->id]) }}" class="btn-military" style="font-size: 0.7rem; padding: 4px 8px;" target="_blank">
                <i class="fa-solid fa-download"></i> Unduh
            </a>
        </div>
    @endif

    {{-- Upload Form (only when status allows it) --}}
    @if(in_array($application->status, ['DRAFT', 'DIAJUKAN', 'PERLU_PERBAIKAN']))
        {{-- Show upload if not uploaded, OR if it was DITOLAK --}}
        @if(!$doc || $doc->status_verifikasi === 'DITOLAK')
        <form action="{{ route('user.pengajuan_nikah.upload_document', $application->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pihak" value="{{ $pihak }}">
            <input type="hidden" name="marriage_document_type_id" value="{{ $req->id }}">
            <input type="hidden" name="jenis_dokumen" value="{{ $req->code }}">
            <div style="display: flex; gap: 6px; align-items: center; margin-top: 6px;">
                <input type="file" name="file" class="form-control" style="padding: 4px 8px; font-size: 0.78rem; flex: 1;" accept=".pdf,.jpg,.jpeg,.png" required>
                <button type="submit" class="btn-military" style="padding: 5px 12px; font-size: 0.78rem; white-space: nowrap;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload
                </button>
            </div>
        </form>
        @elseif($doc && $doc->status_verifikasi === 'BELUM_DIPERIKSA')
        {{-- Allow re-upload if not yet inspected --}}
        <details style="margin-top: 6px;">
            <summary style="font-size: 0.75rem; color: var(--text-muted); cursor: pointer;">Ganti file (opsional)</summary>
            <form action="{{ route('user.pengajuan_nikah.upload_document', $application->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 6px;">
                @csrf
                <input type="hidden" name="pihak" value="{{ $pihak }}">
                <input type="hidden" name="marriage_document_type_id" value="{{ $req->id }}">
                <input type="hidden" name="jenis_dokumen" value="{{ $req->code }}">
                <div style="display: flex; gap: 6px; align-items: center;">
                    <input type="file" name="file" class="form-control" style="padding: 4px 8px; font-size: 0.78rem; flex: 1;" accept=".pdf,.jpg,.jpeg,.png" required>
                    <button type="submit" class="btn-military" style="padding: 5px 12px; font-size: 0.78rem;">Ganti</button>
                </div>
            </form>
        </details>
        @endif
    @endif
</div>
