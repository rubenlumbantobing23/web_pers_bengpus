@extends('layouts.admin')

@section('page-title', 'Arsip Surat Intern Personalia')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengarsipan Surat Intern Personalia</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Upload, cari, dan unduh berkas arsip surat perintah, surat izin cuti/nikah, dan nota dinas</p>
        </div>

        <button type="button" class="btn-military" onclick="openUploadLetterModal()">
            <i class="fa-solid fa-file-arrow-up"></i> Upload Surat Baru
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card" style="padding: 20px;">
        <form action="{{ route('admin.letters.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; align-items: end;">
            <div>
                <label class="form-label" for="search">Cari Nomor Surat / Perihal</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
            </div>

            <div>
                <label class="form-label" for="category">Kategori Surat</label>
                <select id="category" name="category" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-military" style="flex-grow: 1;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Letters Cards Grid -->
    @if($letters->isEmpty())
        <div class="glass-card" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-file-excel" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
            <p>Belum ada arsip surat yang diunggah.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
            @foreach($letters as $letter)
                <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px;">
                            <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                                {{ $letter->category }}
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">
                                {{ $letter->letter_date->format('d/m/Y') }}
                            </span>
                        </div>

                        <h4 style="font-size: 1.05rem; color: #fff; margin-bottom: 6px;">{{ $letter->subject }}</h4>
                        <div style="font-size: 0.85rem; color: var(--accent-gold); font-weight: 600; margin-bottom: 10px;">
                            NO: {{ $letter->letter_number }}
                        </div>

                        @if($letter->description)
                            <p style="font-size: 0.875rem; color: var(--text-sub); line-height: 1.4; margin-bottom: 14px;">{{ Str::limit($letter->description, 100) }}</p>
                        @endif
                    </div>

                    <div style="padding-top: 14px; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">
                            By: {{ $letter->uploader->name ?? 'Admin' }}
                        </span>

                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.letters.download', $letter->id) }}" class="btn-military" style="padding: 6px 12px; font-size: 0.8rem;">
                                <i class="fa-solid fa-download"></i> Unduh
                            </a>

                            <form action="{{ route('admin.letters.destroy', $letter->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip surat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" style="padding: 6px 10px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            {{ $letters->links() }}
        </div>
    @endif

    <!-- Upload Letter Modal -->
    <div id="uploadLetterModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 200; align-items: center; justify-content: center; padding: 20px;">
        <div class="glass-card" style="width: 100%; max-width: 600px; background: var(--bg-card);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <h3 style="font-size: 1.2rem; color: #fff;">Upload Arsip Surat Intern Personalia</h3>
                <button type="button" onclick="closeUploadLetterModal()" style="background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.letters.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="letter_number">Nomor Surat</label>
                        <input type="text" id="letter_number" name="letter_number" class="form-control" placeholder="Contoh: Sprin/120/VIII/2026" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="letter_date">Tanggal Surat</label>
                        <input type="date" id="letter_date" name="letter_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Kategori Surat</label>
                    <select id="category" name="category" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">Perihal / Judul Surat</label>
                    <input type="text" id="subject" name="subject" class="form-control" placeholder="Contoh: Surat Izin Cuti Tahunan Serka Ahmad" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Keterangan Tambahan</label>
                    <textarea id="description" name="description" class="form-control" rows="2" placeholder="Catatan perihal surat..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="file">File Dokumen Surat (PDF / DOC / DOCX / Gambar)</label>
                    <input type="file" id="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small style="color: var(--text-muted); display: block; margin-top: 4px;">Maksimal 10MB.</small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn-secondary" onclick="closeUploadLetterModal()">Batal</button>
                    <button type="submit" class="btn-military">Upload & Arsipkan</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openUploadLetterModal() {
        document.getElementById('uploadLetterModal').style.display = 'flex';
    }

    function closeUploadLetterModal() {
        document.getElementById('uploadLetterModal').style.display = 'none';
    }
</script>
@endpush
@endsection
