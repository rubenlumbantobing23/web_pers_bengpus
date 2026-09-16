@extends('layouts.admin')

@section('page-title', 'Kelola Jenis & Syarat Cuti')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Manajemen Master Jenis & Syarat Cuti</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Atur jenis cuti, aturan jatah, syarat & ketentuan, serta kelengkapan berkas</p>
        </div>

        <button type="button" class="btn-military" onclick="openCreateModal()">
            <i class="fa-solid fa-plus-circle"></i> Tambah Jenis Cuti Baru
        </button>
    </div>

    <!-- Cards List of Leave Types -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 20px;">
        @foreach($leaveTypes as $t)
            <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <h3 style="font-size: 1.2rem; color: #fff;">{{ $t->name }}</h3>
                        <span class="badge" style="background: rgba(5, 150, 105, 0.2); color: #34d399;">
                            {{ $t->default_days }} Hari
                        </span>
                    </div>

                    <div style="font-size: 0.8rem; color: var(--accent-gold); font-weight: 700; margin-bottom: 8px;">
                        KODE: {{ $t->code }} | STATUS: {{ $t->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                    </div>

                    <p style="font-size: 0.9rem; color: var(--text-sub); margin-bottom: 16px;">{{ $t->description }}</p>

                    <div style="background: rgba(9, 13, 24, 0.7); border-radius: 8px; padding: 12px; font-size: 0.85rem; margin-bottom: 12px;">
                        <strong style="color: #fff; display: block; margin-bottom: 4px;">Syarat & Ketentuan:</strong>
                        <div style="color: var(--text-muted); white-space: pre-line;">{{ Str::limit($t->terms_conditions, 150) }}</div>
                    </div>

                    <div style="background: rgba(245, 158, 11, 0.08); border-radius: 8px; padding: 10px; font-size: 0.825rem; color: #fbbf24;">
                        <strong>Dokumen Diperlukan:</strong> {{ $t->required_documents_info ?? '-' }}
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" style="padding: 6px 14px; font-size: 0.85rem;" onclick="openEditModal({{ json_encode($t) }})">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Aturan & Syarat
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Dialog Create / Edit -->
    <div id="leaveTypeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 200; align-items: center; justify-content: center; padding: 20px;">
        <div class="glass-card" style="width: 100%; max-width: 650px; max-height: 90vh; overflow-y: auto; background: var(--bg-card);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <h3 style="font-size: 1.2rem; color: #fff;" id="modal-title">Tambah Jenis Cuti</h3>
                <button type="button" onclick="closeModal()" style="background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>

            <form id="leaveTypeForm" action="{{ route('admin.leave_types.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div class="form-group">
                    <label class="form-label" for="name">Nama Jenis Cuti</label>
                    <input type="text" id="modal-name" name="name" class="form-control" required placeholder="Contoh: Cuti Tahunan">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="code">Kode Unik Sistem</label>
                        <input type="text" id="modal-code" name="code" class="form-control" required placeholder="CT_TAHUNAN">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="default_days">Default Jatah Hari Kerja</label>
                        <input type="number" id="modal-default_days" name="default_days" class="form-control" required value="12">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi Singkat</label>
                    <textarea id="modal-description" name="description" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="terms_conditions">Syarat & Ketentuan (Tampil saat pengajuan user)</label>
                    <textarea id="modal-terms_conditions" name="terms_conditions" class="form-control" rows="4" placeholder="Tuliskan syarat & ketentuan per baris..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="required_documents_info">Informasi Dokumen Wajib</label>
                    <input type="text" id="modal-required_documents_info" name="required_documents_info" class="form-control" placeholder="Contoh: Scan Nota Dinas & Surat Permohonan">
                </div>

                <div class="form-group">
                    <label class="form-label" for="is_active">Status Aktif</label>
                    <select id="modal-is_active" name="is_active" class="form-control">
                        <option value="1">Aktif (Dapat dipilih user)</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-military">Simpan Jenis Cuti</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modal-title').innerText = 'Tambah Jenis Cuti Baru';
        document.getElementById('leaveTypeForm').action = "{{ route('admin.leave_types.store') }}";
        document.getElementById('form-method').value = 'POST';
        
        document.getElementById('modal-name').value = '';
        document.getElementById('modal-code').value = '';
        document.getElementById('modal-default_days').value = '12';
        document.getElementById('modal-description').value = '';
        document.getElementById('modal-terms_conditions').value = '';
        document.getElementById('modal-required_documents_info').value = '';
        document.getElementById('modal-is_active').value = '1';

        document.getElementById('leaveTypeModal').style.display = 'flex';
    }

    function openEditModal(typeObj) {
        document.getElementById('modal-title').innerText = 'Edit Jenis Cuti: ' + typeObj.name;
        document.getElementById('leaveTypeForm').action = "/admin/leave-types/" + typeObj.id;
        document.getElementById('form-method').value = 'PUT';

        document.getElementById('modal-name').value = typeObj.name;
        document.getElementById('modal-code').value = typeObj.code;
        document.getElementById('modal-default_days').value = typeObj.default_days;
        document.getElementById('modal-description').value = typeObj.description || '';
        document.getElementById('modal-terms_conditions').value = typeObj.terms_conditions || '';
        document.getElementById('modal-required_documents_info').value = typeObj.required_documents_info || '';
        document.getElementById('modal-is_active').value = typeObj.is_active ? '1' : '0';

        document.getElementById('leaveTypeModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('leaveTypeModal').style.display = 'none';
    }
</script>
@endpush
@endsection
