@extends('layouts.user')

@section('page-title', 'Formulir Pengajuan Nikah')

@section('user-content')
<div style="max-width: 960px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.4rem; color: #fff; font-weight: 700;">Formulir Pengajuan Nikah Baru</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Anda mengajukan izin nikah sebagai calon <strong style="color: var(--primary);">{{ strtolower($peranAnggota) }}</strong>, dan pasangan akan diperlakukan sebagai calon <strong style="color: var(--primary);">{{ strtolower($peranPasangan) }}</strong>.
            </p>
        </div>
        <a href="{{ route('user.pengajuan_nikah.index') }}" class="btn-military" style="background: rgba(255,255,255,0.08);">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); padding: 14px 18px; border-radius: 10px; color: #fca5a5;">
        <div style="font-weight: 700; margin-bottom: 8px;"><i class="fa-solid fa-circle-exclamation"></i> Terdapat kesalahan input:</div>
        <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('user.pengajuan_nikah.store') }}" method="POST" id="form-nikah">
    @csrf

    {{-- ═══ SECTION 1: DATA ANGGOTA (READONLY) ═══════════════════════════ --}}
    <div class="glass-card" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #059669, #047857); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 0.85rem;">1</div>
            <h3 style="font-size: 1.05rem; color: var(--accent-gold); font-weight: 700;">Data Anggota (Otomatis dari Nominatif)</h3>
        </div>
        <div style="background: rgba(5,150,105,0.05); border: 1px solid rgba(5,150,105,0.15); border-radius: 8px; padding: 14px; margin-bottom: 12px; font-size: 0.85rem; color: #6ee7b7;">
            <i class="fa-solid fa-circle-info"></i> Data di bawah ini diambil secara otomatis dari data nominatif personel Anda dan tidak dapat diubah melalui halaman ini.
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            @php
                $fields = [
                    'Nama Lengkap'       => $personel->nama,
                    'NRP / NIP'          => $personel->nrp_nip,
                    'Pangkat / Golongan' => $personel->pangkat_golongan,
                    'Jabatan'            => $personel->jabatan,
                    'Satuan / Bagian'    => $personel->satuan_bagian,
                    'Corps'              => $personel->corps ?: '-',
                    'Agama'              => $personel->agama ?: '-',
                    'Suku'               => $personel->suku ?: '-',
                    'Tempat Lahir'       => $personel->tempat_lahir ?: '-',
                    'Tanggal Lahir'      => $personel->tgl_lahir ? $personel->tgl_lahir->format('d M Y') : '-',
                    'Jenis Kelamin'      => $personel->jenis_kelamin,
                    'Peran dalam Nikah'  => $peranAnggota,
                ];
            @endphp
            @foreach($fields as $label => $value)
            <div>
                <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">{{ $label }}</label>
                <div style="margin-top: 4px; padding: 8px 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 6px; color: #e2e8f0; font-size: 0.9rem;">{{ $value ?: '-' }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ SECTION 2: DATA RENCANA PERNIKAHAN ════════════════════════════ --}}
    <div class="glass-card" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #7c3aed, #5b21b6); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 0.85rem;">2</div>
            <h3 style="font-size: 1.05rem; color: var(--accent-gold); font-weight: 700;">Data Rencana Pernikahan</h3>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Tanggal Rencana Nikah <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_rencana_nikah" class="form-control" value="{{ old('tanggal_rencana_nikah') }}" required>
            </div>
            <div class="form-group">
                <label>Tempat Pelaksanaan (Gedung / KUA / Rumah) <span class="text-danger">*</span></label>
                <input type="text" name="tempat_nikah" class="form-control" value="{{ old('tempat_nikah') }}" placeholder="Contoh: KUA Kecamatan Cicendo" required>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Alamat Lengkap Tempat Pelaksanaan <span class="text-danger">*</span></label>
                <textarea name="alamat_nikah" class="form-control" rows="2" required>{{ old('alamat_nikah') }}</textarea>
            </div>
            <div class="form-group">
                <label>Kelurahan / Desa <span class="text-danger">*</span></label>
                <input type="text" name="kelurahan_nikah" class="form-control" value="{{ old('kelurahan_nikah') }}" required>
            </div>
            <div class="form-group">
                <label>Kecamatan <span class="text-danger">*</span></label>
                <input type="text" name="kecamatan_nikah" class="form-control" value="{{ old('kecamatan_nikah') }}" required>
            </div>
            <div class="form-group">
                <label>Kabupaten / Kota <span class="text-danger">*</span></label>
                <input type="text" name="kabupaten_nikah" class="form-control" value="{{ old('kabupaten_nikah') }}" required>
            </div>
            <div class="form-group">
                <label>Provinsi <span class="text-danger">*</span></label>
                <input type="text" name="provinsi_nikah" class="form-control" value="{{ old('provinsi_nikah') }}" required>
            </div>
        </div>
    </div>

    {{-- ═══ SECTION 3: DATA CALON PASANGAN ════════════════════════════════ --}}
    <div class="glass-card" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #db2777, #9d174d); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 0.85rem;">3</div>
            <h3 style="font-size: 1.05rem; color: var(--accent-gold); font-weight: 700;">Data {{ $peranPasangan }}</h3>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_nama" class="form-control" value="{{ old('pasangan_nama') }}" required>
            </div>
            <div class="form-group">
                <label>Agama <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_agama" class="form-control" value="{{ old('pasangan_agama') }}" required>
            </div>
            <div class="form-group">
                <label>Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_tempat_lahir" class="form-control" value="{{ old('pasangan_tempat_lahir') }}" required>
            </div>
            <div class="form-group">
                <label>Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="pasangan_tanggal_lahir" class="form-control" value="{{ old('pasangan_tanggal_lahir') }}" required>
            </div>
            <div class="form-group">
                <label>Suku Bangsa <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_suku" class="form-control" value="{{ old('pasangan_suku') }}" required>
            </div>
            <div class="form-group">
                <label>Status Pekerjaan <span class="text-danger">*</span></label>
                <select name="pasangan_status_pekerjaan" class="form-control" id="status-pekerjaan-select" onchange="toggleAsnFields(this.value)" required>
                    <option value="">-- Pilih Status Pekerjaan --</option>
                    <option value="Non-ASN" {{ old('pasangan_status_pekerjaan') == 'Non-ASN' ? 'selected' : '' }}>Non-ASN / Swasta / Lainnya</option>
                    <option value="ASN" {{ old('pasangan_status_pekerjaan') == 'ASN' ? 'selected' : '' }}>ASN / TNI / POLRI</option>
                </select>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Pekerjaan (Deskripsi Lengkap) <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_pekerjaan" class="form-control" value="{{ old('pasangan_pekerjaan') }}" placeholder="Contoh: Karyawan Swasta, Wiraswasta, PNS Guru, dll" required>
            </div>

            {{-- ASN-only fields --}}
            <div class="form-group asn-only" style="display: {{ old('pasangan_status_pekerjaan') == 'ASN' ? 'block' : 'none' }};">
                <label>Instansi / Satuan Kerja <span class="text-danger asn-required">*</span></label>
                <input type="text" name="pasangan_instansi" class="form-control" value="{{ old('pasangan_instansi') }}" {{ old('pasangan_status_pekerjaan') == 'ASN' ? 'required' : '' }}>
            </div>
            <div class="form-group asn-only" style="display: {{ old('pasangan_status_pekerjaan') == 'ASN' ? 'block' : 'none' }};">
                <label>Jabatan di Instansi <span class="text-danger asn-required">*</span></label>
                <input type="text" name="pasangan_jabatan" class="form-control" value="{{ old('pasangan_jabatan') }}" {{ old('pasangan_status_pekerjaan') == 'ASN' ? 'required' : '' }}>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea name="pasangan_alamat" class="form-control" rows="2" required>{{ old('pasangan_alamat') }}</textarea>
            </div>
            <div class="form-group">
                <label>Kelurahan / Desa <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_kelurahan" class="form-control" value="{{ old('pasangan_kelurahan') }}" required>
            </div>
            <div class="form-group">
                <label>Kecamatan <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_kecamatan" class="form-control" value="{{ old('pasangan_kecamatan') }}" required>
            </div>
            <div class="form-group">
                <label>Kabupaten / Kota <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_kabupaten" class="form-control" value="{{ old('pasangan_kabupaten') }}" required>
            </div>
            <div class="form-group">
                <label>Provinsi <span class="text-danger">*</span></label>
                <input type="text" name="pasangan_provinsi" class="form-control" value="{{ old('pasangan_provinsi') }}" required>
            </div>
        </div>
    </div>

    {{-- ═══ SECTION 4: DATA ORANG TUA / WALI PASANGAN ═════════════════════ --}}
    <div class="glass-card" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #d97706, #b45309); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 0.85rem;">4</div>
            <h3 style="font-size: 1.05rem; color: var(--accent-gold); font-weight: 700;">Data Orang Tua / Wali {{ $peranPasangan }}</h3>
        </div>

        <p style="font-size: 0.85rem; font-weight: 700; color: #60a5fa; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
            <i class="fa-solid fa-person"></i> Data Bapak / Wali
        </p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group">
                <label>Nama Bapak / Wali <span class="text-danger">*</span></label>
                <input type="text" name="bapak_nama" class="form-control" value="{{ old('bapak_nama') }}" required>
            </div>
            <div class="form-group">
                <label>Agama <span class="text-danger">*</span></label>
                <input type="text" name="bapak_agama" class="form-control" value="{{ old('bapak_agama') }}" required>
            </div>
            <div class="form-group">
                <label>Pekerjaan <span class="text-danger">*</span></label>
                <input type="text" name="bapak_pekerjaan" class="form-control" value="{{ old('bapak_pekerjaan') }}" required>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="bapak_alamat" class="form-control" value="{{ old('bapak_alamat') }}" required>
            </div>
        </div>

        <p style="font-size: 0.85rem; font-weight: 700; color: #f472b6; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
            <i class="fa-solid fa-person-dress"></i> Data Ibu
        </p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Nama Ibu <span class="text-danger">*</span></label>
                <input type="text" name="ibu_nama" class="form-control" value="{{ old('ibu_nama') }}" required>
            </div>
            <div class="form-group">
                <label>Agama <span class="text-danger">*</span></label>
                <input type="text" name="ibu_agama" class="form-control" value="{{ old('ibu_agama') }}" required>
            </div>
            <div class="form-group">
                <label>Pekerjaan <span class="text-danger">*</span></label>
                <input type="text" name="ibu_pekerjaan" class="form-control" value="{{ old('ibu_pekerjaan') }}" required>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="ibu_alamat" class="form-control" value="{{ old('ibu_alamat') }}" required>
            </div>
        </div>
    </div>

    {{-- Submit Buttons --}}
    <div style="display: flex; justify-content: flex-end; gap: 12px; padding: 20px 0;">
        <a href="{{ route('user.pengajuan_nikah.index') }}" class="btn-military" style="background: rgba(255,255,255,0.08);">
            <i class="fa-solid fa-xmark"></i> Batal
        </a>
        <button type="submit" form="form-nikah" name="_action" value="draft" formaction="{{ route('user.pengajuan_nikah.save_draft') }}" class="btn-military" style="background: rgba(100,116,139,0.4); border-color: rgba(100,116,139,0.6);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan sebagai Draft
        </button>
        <button type="submit" class="btn-military">
            <i class="fa-solid fa-paper-plane"></i> Ajukan ke Admin
        </button>
    </div>

    </form>

</div>

<script>
function toggleAsnFields(val) {
    document.querySelectorAll('.asn-only').forEach(el => {
        el.style.display = val === 'ASN' ? 'block' : 'none';
        const input = el.querySelector('input');
        if (input) {
            if (val === 'ASN') {
                input.setAttribute('required', 'required');
            } else {
                input.removeAttribute('required');
                input.value = '';
            }
        }
    });
}
// Initialize on page load with old value
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('status-pekerjaan-select');
    if (sel && sel.value) toggleAsnFields(sel.value);
});
</script>
@endsection
