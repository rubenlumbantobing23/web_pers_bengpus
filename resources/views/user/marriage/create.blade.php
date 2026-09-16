@extends('layouts.user')

@section('page-title', 'Form Pengajuan Izin Nikah')

@section('user-content')
<div style="max-width: 850px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Title Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Permohonan Izin Nikah Anggota</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Lengkapi data calon pasangan dan lampirkan dokumen kelengkapan administrasi</p>
        </div>
        <a href="{{ route('user.marriage.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Syarat & Ketentuan Requirements Info Card (Section 11 Requirement) -->
    <div class="glass-card" style="border-color: rgba(245, 158, 11, 0.3); background: linear-gradient(135deg, rgba(217, 119, 6, 0.1) 0%, var(--glass-bg) 100%);">
        <h3 style="font-size: 1.1rem; color: #fbbf24; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-clipboard-check"></i> Persyaratan Administrasi Pernikahan TNI AD / Pers
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
            @foreach($requirements as $idx => $req)
                <div style="display: flex; align-items: flex-start; gap: 10px; font-size: 0.875rem; color: var(--text-sub);">
                    <span style="color: #fbbf24; font-weight: 700;">{{ $idx + 1 }}.</span>
                    <div>
                        <strong style="color: #fff;">{{ $req->title }}</strong>
                        @if($req->description)
                            <span style="color: var(--text-muted);"> — {{ $req->description }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Gagal Mengirim Pengajuan:</strong>
                <ul style="margin-top: 4px; padding-left: 18px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Marriage Request Form -->
    <form action="{{ route('user.marriage.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Data Calon Mempelai & Pelaksanaan
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="spouse_name">Nama Lengkap Calon Suami / Istri</label>
                    <input type="text" id="spouse_name" name="spouse_name" class="form-control" placeholder="Nama lengkap sesuai KTP" value="{{ old('spouse_name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="spouse_nrp_nip">NRP / NIP Calon Pasangan (Opsional jika TNI/Polri/PNS)</label>
                    <input type="text" id="spouse_nrp_nip" name="spouse_nrp_nip" class="form-control" placeholder="Kosongkan jika Swasta" value="{{ old('spouse_nrp_nip') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="spouse_occupation">Pekerjaan / Instansi Calon Pasangan</label>
                    <input type="text" id="spouse_occupation" name="spouse_occupation" class="form-control" placeholder="Contoh: Karyawan Swasta / PNS / Dokter" value="{{ old('spouse_occupation') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="marriage_date">Tanggal Rencana Pernikahan (Akad / Resepsi)</label>
                    <input type="date" id="marriage_date" name="marriage_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('marriage_date') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="marriage_location">Lokasi Tempat Pelaksanaan Akad / Resepsi</label>
                    <input type="text" id="marriage_location" name="marriage_location" class="form-control" placeholder="Contoh: Gedung Balai Prajurit / KUA Kec. Coblong Bandung" value="{{ old('marriage_location') }}" required>
                </div>
            </div>

            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Lampiran Dokumen Persyaratan
            </h3>

            <div class="form-group">
                <label class="form-label" for="documents">Upload File Dokumen Persyaratan (PDF / JPG / PNG)</label>
                <input type="file" id="documents" name="documents[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                <small style="color: var(--text-muted); display: block; margin-top: 6px;">Unggah gabungan file N1-N4, SKBM, SKCK, Foto PDH, KTP/KK/Akta (Maksimal 5MB per file).</small>
            </div>

            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 16px; margin: 24px 0;">
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                    <input type="checkbox" name="terms_accepted" id="terms_accepted" value="1" style="width: 20px; height: 20px; accent-color: var(--primary); margin-top: 2px;" required>
                    <span style="font-size: 0.9rem; color: #fef08a;">
                        Saya menyatakan bahwa seluruh data calon pasangan dan dokumen permohonan izin nikah di atas adalah benar dan sesuai dengan ketentuan persetujuan komando.
                    </span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="submit" class="btn-military" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); padding: 14px 32px;">
                    <i class="fa-solid fa-paper-plane"></i> KIRIM PENGAJUAN NIKAH
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
