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
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">NRP / NIP</label>
                    <input type="text" class="form-control" value="{{ $personel->nrp_nip ?? '-' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <input type="text" class="form-control" value="{{ $personel->jenis_kelamin ?? '-' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">Pangkat / Golongan</label>
                    <input type="text" class="form-control" value="{{ $personel->pangkat_golongan ?? '-' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Personel</label>
                    <input type="text" class="form-control" value="{{ $personel->kategori_personel ?? '-' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" value="{{ $personel->jabatan ?? '-' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">Satuan / Bagian</label>
                    <input type="text" class="form-control" value="{{ $personel->satuan_bagian ?? 'Bengpuskomlekad' }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>

                <div class="form-group" style="grid-column: span 2; margin-top: 10px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                    <h4 style="font-size: 1rem; color: #fff; margin-bottom: 12px;">Data yang Dapat Diubah</h4>
                </div>

                @if(!$isPejabat)
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="organization_unit_id">Atasan Langsung *</label>
                    <select id="organization_unit_id" name="organization_unit_id" class="form-control" required style="appearance: none; padding-right: 30px;">
                        <option value="">-- Pilih Atasan Langsung --</option>
                        @foreach($officials as $official)
                            <option value="{{ $official->organization_unit_id }}" {{ old('organization_unit_id', $personel->organization_unit_id) == $official->organization_unit_id ? 'selected' : '' }}>
                                {{ strtoupper($official->roleLabel) }} — {{ $official->personel ? $official->personel->nama : 'Belum Ada Pejabat' }}
                            </option>
                        @endforeach
                    </select>
                    @if($supervisor)
                        <small style="color: #34d399; display: block; margin-top: 8px; font-size: 0.8rem;">
                            <i class="fa-solid fa-circle-check"></i> Atasan Anda saat ini: <strong>{{ $supervisor->nama }}</strong> ({{ $supervisor->jabatan }})
                        </small>
                    @endif
                </div>
                @endif

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
