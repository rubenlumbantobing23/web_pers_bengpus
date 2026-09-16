@extends('layouts.user')

@section('page-title', 'Profil Saya')

@section('user-content')
<div style="max-width: 750px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Title Bar -->
    <div>
        <h2 style="font-size: 1.5rem; color: #fff;">Profil Personel / Anggota</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola data pribadi dan informasi akun login Anda</p>
    </div>

    <!-- Profile Form Card -->
    <div class="glass-card">
        <form action="{{ route('user.profile.update') }}" method="POST">
            @csrf
            
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #059669, #047857); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: 800; box-shadow: 0 4px 20px rgba(5, 150, 105, 0.4);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h3 style="font-size: 1.3rem; color: #fff;">{{ $user->name }}</h3>
                    <div style="font-size: 0.9rem; color: var(--primary); font-weight: 600; margin-top: 2px;">
                        {{ $personel->pangkat_golongan ?? 'Anggota' }} — {{ $personel->nrp_nip ?? '-' }}
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="nrp_nip">NRP / NIP (Tetap)</label>
                    <input type="text" class="form-control" value="{{ $personel->nrp_nip ?? '-' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label" for="jenis_kelamin">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Pria" {{ old('jenis_kelamin', $personel->jenis_kelamin ?? '') === 'Pria' ? 'selected' : '' }}>Pria</option>
                        <option value="Wanita" {{ old('jenis_kelamin', $personel->jenis_kelamin ?? '') === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pangkat_golongan">Pangkat / Golongan</label>
                    <input type="text" id="pangkat_golongan" name="pangkat_golongan" class="form-control" value="{{ old('pangkat_golongan', $personel->pangkat_golongan ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="kategori_personel">Kategori Pemohon *</label>
                    <select id="kategori_personel" name="kategori_personel" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Perwira Menengah" {{ old('kategori_personel', $personel->kategori_personel ?? '') === 'Perwira Menengah' ? 'selected' : '' }}>Perwira Menengah</option>
                        <option value="Perwira Pertama" {{ old('kategori_personel', $personel->kategori_personel ?? '') === 'Perwira Pertama' ? 'selected' : '' }}>Perwira Pertama</option>
                        <option value="Bintara" {{ old('kategori_personel', $personel->kategori_personel ?? '') === 'Bintara' ? 'selected' : '' }}>Bintara</option>
                        <option value="Tamtama" {{ old('kategori_personel', $personel->kategori_personel ?? '') === 'Tamtama' ? 'selected' : '' }}>Tamtama</option>
                        <option value="PNS" {{ old('kategori_personel', $personel->kategori_personel ?? '') === 'PNS' ? 'selected' : '' }}>PNS</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="jabatan">Jabatan</label>
                    <input type="text" id="jabatan" name="jabatan" class="form-control" value="{{ old('jabatan', $personel->jabatan ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="satuan_bagian">Satuan / Bagian</label>
                    <input type="text" id="satuan_bagian" name="satuan_bagian" class="form-control" value="{{ old('satuan_bagian', $personel->satuan_bagian ?? 'Bengpuskomlekad') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="no_hp">Nomor Telepon / WA</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control" value="{{ old('no_hp', $personel->no_hp ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Login</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group" style="grid-column: span 2; margin-top: 10px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                    <h4 style="font-size: 1rem; color: #fff; margin-bottom: 12px;">Ubah Kata Sandi (Kosongkan jika tidak ingin diubah)</h4>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password Baru</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter" style="padding-right: 40px;">
                        <i class="fa-solid fa-eye" id="togglePasswordProf" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="const input = document.getElementById('password'); input.type = input.type === 'password' ? 'text' : 'password'; this.classList.toggle('fa-eye-slash');"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                    <div style="position: relative;">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" style="padding-right: 40px;">
                        <i class="fa-solid fa-eye" id="togglePasswordConfProf" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="const input = document.getElementById('password_confirmation'); input.type = input.type === 'password' ? 'text' : 'password'; this.classList.toggle('fa-eye-slash');"></i>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="submit" class="btn-military" style="padding: 12px 28px;">
                    <i class="fa-solid fa-floppy-disk"></i> SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
