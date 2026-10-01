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
        <form action="{{ route('admin.letters.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
            <div>
                <label class="form-label" for="search">Cari Arsip</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="No surat, perihal, pengirim..." value="{{ request('search') }}">
            </div>

            <div>
                <label class="form-label" for="direction">Arah Surat</label>
                <select id="direction" name="direction" class="form-control" onchange="this.form.submit()">
                    <option value="Semua" {{ request('direction') === 'Semua' ? 'selected' : '' }}>Semua Arah</option>
                    <option value="MASUK" {{ request('direction') === 'MASUK' ? 'selected' : '' }}>Surat Masuk</option>
                    <option value="KELUAR" {{ request('direction') === 'KELUAR' ? 'selected' : '' }}>Surat Keluar</option>
                </select>
            </div>

            <div>
                <label class="form-label" for="letter_type_id">Jenis Surat</label>
                <select id="letter_type_id" name="letter_type_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    @foreach($letterTypes as $type)
                        <option value="{{ $type->id }}" {{ request('letter_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" for="classification">Klasifikasi</label>
                <select id="classification" name="classification" class="form-control" onchange="this.form.submit()">
                    <option value="Semua" {{ request('classification') === 'Semua' ? 'selected' : '' }}>Semua Klasifikasi</option>
                    <option value="BIASA" {{ request('classification') === 'BIASA' ? 'selected' : '' }}>Biasa</option>
                    <option value="RAHASIA" {{ request('classification') === 'RAHASIA' ? 'selected' : '' }}>Rahasia</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-military" style="flex-grow: 1;">
                    <i class="fa-solid fa-filter"></i> Terapkan
                </button>
            </div>
        </form>
    </div>

    <!-- Letters Cards Grid -->
    @if($letters->isEmpty())
        <div class="glass-card" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-file-excel" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
            <p>Tidak ada arsip surat yang ditemukan.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
            @foreach($letters as $letter)
                <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                    @if($letter->classification === 'RAHASIA')
                        <div style="position: absolute; top: 10px; right: 10px; color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; border: 1px solid rgba(239, 68, 68, 0.2);">
                            <i class="fa-solid fa-lock"></i> RAHASIA
                        </div>
                    @endif
                    <div>
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; padding-right: {{ $letter->classification === 'RAHASIA' ? '70px' : '0' }};">
                            <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                                {{ $letter->letterType ? $letter->letterType->name : $letter->category }}
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">
                                {{ $letter->letter_date->format('d/m/Y') }}
                            </span>
                        </div>

                        <h4 style="font-size: 1.05rem; color: #fff; margin-bottom: 6px;">{{ $letter->subject }}</h4>
                        <div style="font-size: 0.85rem; color: var(--accent-gold); font-weight: 600; margin-bottom: 10px;">
                            NO: {{ $letter->letter_number }}
                        </div>

                        <div style="font-size: 0.8rem; margin-bottom: 10px;">
                            <span style="color: var(--text-sub);">
                                @if($letter->direction === 'MASUK')
                                    <div style="margin-bottom: 4px;">
                                        <i class="fa-solid fa-arrow-right-to-bracket" style="color: #4ade80;"></i> Masuk dari: <strong>{{ $letter->sender ?? '-' }}</strong>
                                    </div>
                                    @if($letter->received_date)
                                        <div><i class="fa-solid fa-calendar-check" style="color: #4ade80;"></i> Diterima: <strong>{{ \Carbon\Carbon::parse($letter->received_date)->format('d/m/Y') }}</strong></div>
                                    @endif
                                @elseif($letter->direction === 'KELUAR')
                                    <div>
                                        <i class="fa-solid fa-arrow-right-from-bracket" style="color: #f87171;"></i> Tujuan: <strong>{{ $letter->recipient ?? '-' }}</strong>
                                    </div>
                                @else
                                    <i class="fa-solid fa-file" style="color: #94a3b8;"></i> Arah: <strong>-</strong>
                                @endif
                            </span>
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
                            @if($letter->classification === 'RAHASIA')
                                <a href="{{ route('admin.letters.secret.verify_form', $letter->id) }}" class="btn-danger" style="padding: 6px 12px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-lock"></i> Akses Arsip
                                </a>
                            @else
                                <a href="{{ route('admin.letters.download', $letter->id) }}" class="btn-military" style="padding: 6px 12px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-download"></i> Unduh
                                </a>
                            @endif

                            <form action="{{ route('admin.letters.destroy', $letter->id) }}" method="POST" data-confirm="Arsip surat ini akan dihapus." data-confirm-title="Hapus arsip surat?" data-confirm-button="Ya, hapus">
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
        <div class="glass-card" style="width: 100%; max-width: 700px; background: var(--bg-card); max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <h3 style="font-size: 1.2rem; color: #fff;">Upload Arsip Surat Intern Personalia</h3>
                <button type="button" onclick="closeUploadLetterModal()" style="background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.letters.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="direction_upload">Arah Surat</label>
                        <select id="direction_upload" name="direction" class="form-control" required onchange="toggleDirectionFields()">
                            <option value="">-- Pilih Arah --</option>
                            <option value="MASUK">Surat Masuk</option>
                            <option value="KELUAR">Surat Keluar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="classification_upload">Klasifikasi Surat</label>
                        <select id="classification_upload" name="classification" class="form-control" required>
                            <option value="BIASA">Biasa</option>
                            <option value="RAHASIA">Rahasia</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="letter_number">Nomor Surat</label>
                        <input type="text" id="letter_number" name="letter_number" class="form-control" placeholder="Contoh: Sprin/120/VIII/2026" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="letter_type_id_upload">Jenis Surat</label>
                        <select id="letter_type_id_upload" name="letter_type_id" class="form-control" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($letterTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="letter_date">Tanggal Surat</label>
                        <input type="date" id="letter_date" name="letter_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group" id="received_date_group" style="display: none;">
                        <label class="form-label" for="received_date">Tanggal Diterima</label>
                        <input type="date" id="received_date" name="received_date" class="form-control">
                    </div>
                </div>

                <div class="form-group" id="sender_group" style="display: none;">
                    <label class="form-label" for="sender">Asal Surat</label>
                    <input type="text" id="sender" name="sender" class="form-control" placeholder="Instansi/Orang pengirim surat">
                </div>

                <div class="form-group" id="recipient_group" style="display: none;">
                    <label class="form-label" for="recipient">Tujuan Surat</label>
                    <input type="text" id="recipient" name="recipient" class="form-control" placeholder="Instansi/Orang tujuan surat">
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
        toggleDirectionFields();
    }

    function closeUploadLetterModal() {
        document.getElementById('uploadLetterModal').style.display = 'none';
    }

    function toggleDirectionFields() {
        const direction = document.getElementById('direction_upload').value;
        const senderGroup = document.getElementById('sender_group');
        const recipientGroup = document.getElementById('recipient_group');
        const receivedDateGroup = document.getElementById('received_date_group');

        const senderInput = document.getElementById('sender');
        const recipientInput = document.getElementById('recipient');
        const receivedDateInput = document.getElementById('received_date');

        if (direction === 'MASUK') {
            senderGroup.style.display = 'block';
            receivedDateGroup.style.display = 'block';
            recipientGroup.style.display = 'none';

            senderInput.required = true;
            receivedDateInput.required = true;
            recipientInput.required = false;
        } else if (direction === 'KELUAR') {
            senderGroup.style.display = 'none';
            receivedDateGroup.style.display = 'none';
            recipientGroup.style.display = 'block';

            senderInput.required = false;
            receivedDateInput.required = false;
            recipientInput.required = true;
        } else {
            senderGroup.style.display = 'none';
            receivedDateGroup.style.display = 'none';
            recipientGroup.style.display = 'none';

            senderInput.required = false;
            receivedDateInput.required = false;
            recipientInput.required = false;
        }
    }
</script>
@endpush
@endsection
