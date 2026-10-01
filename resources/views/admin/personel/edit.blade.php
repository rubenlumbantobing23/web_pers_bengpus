@extends('layouts.admin')

@section('page-title', 'Edit Data Personel')

@section('admin-content')
<div style="max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Edit Data Personel: {{ $personel->nama }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Perbarui data nominatif atau ubah status keaktifan</p>
        </div>
        <a href="{{ route('admin.personel.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Gagal Menyimpan:</strong>
                <ul style="margin-top: 4px; padding-left: 18px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.personel.update', $personel->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- A. JENIS PERSONEL (HIDDEN) --}}
        <input type="hidden" name="jenis_personel" value="{{ $personel->jenis_personel }}">

        <div class="glass-card" style="margin-bottom:20px; text-align:center;">
            <span style="font-size: 1.2rem; font-weight: bold; color: {{ $personel->jenis_personel === 'militer' ? '#60a5fa' : '#fbbf24' }};">
                <i class="fa-solid {{ $personel->jenis_personel === 'militer' ? 'fa-shield-halved' : 'fa-briefcase' }}"></i> 
                DATA {{ strtoupper($personel->jenis_personel) }}
            </span>
        </div>

        {{-- B. IDENTITAS --}}
        <div class="glass-card" style="margin-bottom:20px;">
            <h4 style="color:var(--accent-gold); font-size:0.85rem; letter-spacing:1px; text-transform:uppercase; margin-bottom:16px;">
                <i class="fa-solid fa-id-card"></i> Identitas
            </h4>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label" for="nama">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama', $personel->nama) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="nrp_nip" id="label-nrp">{{ $personel->jenis_personel === 'militer' ? 'NRP *' : 'NIP *' }}</label>
                    <input type="text" id="nrp_nip" name="nrp_nip" class="form-control" value="{{ old('nrp_nip', $personel->nrp_nip) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                    @php
                        $jk = strtolower(trim($personel->jenis_kelamin ?? ''));
                        $isPria = in_array($jk, ['pria', 'laki-laki', 'l']);
                        $isWanita = in_array($jk, ['wanita', 'perempuan', 'p', 'w']);
                        $mappedJk = $isPria ? 'Pria' : ($isWanita ? 'Wanita' : $personel->jenis_kelamin);
                    @endphp
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Pria" {{ old('jenis_kelamin', $mappedJk) === 'Pria' ? 'selected' : '' }}>Pria</option>
                        <option value="Wanita" {{ old('jenis_kelamin', $mappedJk) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status_pernikahan">Status Pernikahan</label>
                    @php
                        $sn = strtolower(trim($personel->status_pernikahan ?? ''));
                        $mappedNikah = $personel->status_pernikahan;
                        if (in_array($sn, ['belum menikah', 'belum kawin', 'tk', 'tidak kawin'])) $mappedNikah = 'Belum Menikah';
                        elseif (in_array($sn, ['menikah', 'kawin', 'k'])) $mappedNikah = 'Menikah';
                        elseif (in_array($sn, ['cerai hidup', 'ch'])) $mappedNikah = 'Cerai Hidup';
                        elseif (in_array($sn, ['cerai mati', 'cm'])) $mappedNikah = 'Cerai Mati';
                    @endphp
                    <select id="status_pernikahan" name="status_pernikahan" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Belum Menikah" {{ old('status_pernikahan', $mappedNikah) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="Menikah" {{ old('status_pernikahan', $mappedNikah) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Cerai Hidup" {{ old('status_pernikahan', $mappedNikah) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('status_pernikahan', $mappedNikah) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="tgl_lahir">Tanggal Lahir</label>
                    @php
                        $tglLahirVal = $personel->tgl_lahir ? (strtotime($personel->tgl_lahir) ? date('Y-m-d', strtotime($personel->tgl_lahir)) : $personel->tgl_lahir) : '';
                    @endphp
                    <input type="date" id="tgl_lahir" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $tglLahirVal) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $personel->tempat_lahir) }}">
                </div>
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label" for="agama_suku">Suku / Agama (pisah dengan baris baru)</label>
                    <textarea id="agama_suku" name="agama_suku" class="form-control" rows="2"
                        placeholder="Contoh baris 1: Jawa&#10;Baris 2: Islam">{{ old('agama_suku', $personel->agama_suku) }}</textarea>
                </div>
            </div>
        </div>

        {{-- C. KEPANGKATAN --}}
        <div class="glass-card" style="margin-bottom:20px;">
            <h4 style="color:var(--accent-gold); font-size:0.85rem; letter-spacing:1px; text-transform:uppercase; margin-bottom:16px;">
                <i class="fa-solid fa-star"></i> Kepangkatan / Kepegawaian
            </h4>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label" for="pangkat_golongan" id="label-pangkat">{{ $personel->jenis_personel === 'militer' ? 'Pangkat *' : 'Golongan *' }}</label>
                    <input type="text" id="pangkat_golongan" name="pangkat_golongan" class="form-control" value="{{ old('pangkat_golongan', $personel->pangkat_golongan) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="kategori_personel">Kategori Pemohon *</label>
                    <select id="kategori_personel" name="kategori_personel" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Perwira Menengah" {{ old('kategori_personel', $personel->kategori_personel) === 'Perwira Menengah' ? 'selected' : '' }}>Perwira Menengah</option>
                        <option value="Perwira Pertama" {{ old('kategori_personel', $personel->kategori_personel) === 'Perwira Pertama' ? 'selected' : '' }}>Perwira Pertama</option>
                        <option value="Bintara" {{ old('kategori_personel', $personel->kategori_personel) === 'Bintara' ? 'selected' : '' }}>Bintara</option>
                        <option value="Tamtama" {{ old('kategori_personel', $personel->kategori_personel) === 'Tamtama' ? 'selected' : '' }}>Tamtama</option>
                        <option value="PNS" {{ old('kategori_personel', $personel->kategori_personel) === 'PNS' ? 'selected' : '' }}>PNS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="tmt_pangkat">TMT Pangkat</label>
                    <input type="text" id="tmt_pangkat" name="tmt_pangkat" class="form-control" placeholder="01-01-2023" value="{{ old('tmt_pangkat', $personel->tmt_pangkat) }}">
                </div>
                <div class="form-group" id="group-corps">
                    <label class="form-label" for="corps">Corps</label>
                    <input type="text" id="corps" name="corps" class="form-control" value="{{ old('corps', $personel->corps) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="mkg">MKG (Masa Kerja Golongan)</label>
                    <input type="text" id="mkg" name="mkg" class="form-control" value="{{ old('mkg', $personel->mkg) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="jabatan">Jabatan *</label>
                    <input type="text" id="jabatan" name="jabatan" class="form-control" value="{{ old('jabatan', $personel->jabatan) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="tmt_jabatan">TMT Jabatan</label>
                    <input type="text" id="tmt_jabatan" name="tmt_jabatan" class="form-control" value="{{ old('tmt_jabatan', $personel->tmt_jabatan) }}">
                </div>
                @php
                    $isPejabat = \App\Models\OrganizationOfficialAssignment::where('personel_id', $personel->id)->where('is_active', true)->exists();
                @endphp
                @if($isPejabat)
                <div class="form-group">
                    <label class="form-label">Atasan Langsung</label>
                    <div style="padding: 10px 14px; background: rgba(255,255,255,0.05); border-radius: 8px; color: var(--text-muted); font-size: 0.9rem; border: 1px dashed rgba(255,255,255,0.1);">
                        <i class="fa-solid fa-circle-info" style="color: #60a5fa; margin-right: 6px;"></i> Personel ini menjabat secara struktural (Pejabat), Atasan Langsung ditentukan otomatis.
                    </div>
                </div>
                @else
                <div class="form-group">
                    <label class="form-label" for="organization_unit_id">Atasan Langsung *</label>
                    <select id="organization_unit_id" name="organization_unit_id" class="form-control" required>
                        <option value="">-- Pilih Atasan --</option>
                        @foreach($officials as $official)
                            <option value="{{ $official->organization_unit_id }}" {{ old('organization_unit_id', $personel->organization_unit_id) == $official->organization_unit_id ? 'selected' : '' }}>
                                {{ $official->roleLabel }} - {{ $official->personel ? $official->personel->nama : 'Belum Ada Pejabat' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" style="color: rgba(255, 255, 255, 0.5) !important;">Jika dipilih, pejabat ini otomatis menjadi penandatangan cuti.</small>
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label" for="satuan_bagian">Satuan (Teks Bebas) *</label>
                    <input type="text" id="satuan_bagian" name="satuan_bagian" class="form-control" value="{{ old('satuan_bagian', $personel->satuan_bagian) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="tmt_tni_pa" id="label-tmt-tni">{{ $personel->jenis_personel === 'militer' ? 'TMT TNI/PA' : 'TMT PNS' }}</label>
                    <input type="text" id="tmt_tni_pa" name="tmt_tni_pa" class="form-control" value="{{ old('tmt_tni_pa', $personel->tmt_tni_pa) }}">
                </div>
            </div>
        </div>

        {{-- D. PENDIDIKAN --}}
        <div class="glass-card" style="margin-bottom:20px;">
            <h4 style="color:var(--accent-gold); font-size:0.85rem; letter-spacing:1px; text-transform:uppercase; margin-bottom:16px;">
                <i class="fa-solid fa-graduation-cap"></i> Pendidikan
            </h4>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label" for="dikum_ti">Pendidikan Umum (DIKUM)</label>
                    <input type="text" id="dikum_ti" name="dikum_ti" class="form-control" value="{{ old('dikum_ti', $personel->dikum_ti) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="thn_lulus_dikum">Tahun Lulus DIKUM</label>
                    <input type="text" id="thn_lulus_dikum" name="thn_lulus_dikum" class="form-control" placeholder="2005" value="{{ old('thn_lulus_dikum', $personel->thn_lulus_dikum) }}">
                </div>
                @if($personel->jenis_personel === 'militer')
                <div class="form-group" id="group-dikmil">
                    <label class="form-label" for="dikmit_tni" id="label-dikmil">DIK PERTAMA TNI</label>
                    <input type="text" id="dikmit_tni" name="dikmit_tni" class="form-control" value="{{ old('dikmit_tni', $personel->dikmit_tni) }}">
                </div>
                <div class="form-group" id="group-thn-dikmil">
                    <label class="form-label" for="thn_lulus_dikmit" id="label-thn-dikmil">TAHUN LULUS DIK PERTAMA TNI</label>
                    <input type="text" id="thn_lulus_dikmit" name="thn_lulus_dikmit" class="form-control" value="{{ old('thn_lulus_dikmit', $personel->thn_lulus_dikmit) }}">
                </div>
                @endif
                {{-- Dynamic Repeater for DIKMIL TI --}}
                <div style="grid-column: span 2; margin-top: 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 18px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:10px;">
                        <div>
                            <label class="form-label" style="font-weight:700; color:#fff; font-size:0.95rem; margin:0;" id="label-dikmil-ti-title">
                                DIKMIL TI (Dapat Tambah Lebih dari 1 Pendidikan)
                            </label>
                            <div style="font-size:0.78rem; color:var(--text-muted);">Tambahkan daftar pendidikan militer / lanjutan beserta tahun lulus secara terpisah</div>
                        </div>
                        <button type="button" class="btn-secondary" style="padding:6px 14px; font-size:0.82rem; color:#34d399; border-color:rgba(52,211,153,0.4); background:rgba(52,211,153,0.1);" onclick="addDikmilTiRow()">
                            <i class="fa-solid fa-plus"></i> Tambah Pendidikan
                        </button>
                    </div>

                    <div id="dikmil-ti-container" style="display:flex; flex-direction:column; gap:12px;">
                        <!-- Dynamic Rows -->
                    </div>
                </div>
            </div>
        </div>

        {{-- E. KETERANGAN & AKUN --}}
        <div class="glass-card" style="margin-bottom:20px;">
            <h4 style="color:var(--accent-gold); font-size:0.85rem; letter-spacing:1px; text-transform:uppercase; margin-bottom:16px;">
                <i class="fa-solid fa-note-sticky"></i> Keterangan & Kontak
            </h4>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label" for="ket">Keterangan</label>
                    <input type="text" id="ket" name="ket" class="form-control" value="{{ old('ket', $personel->ket) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="no_hp">Nomor HP / WA</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control" placeholder="08123456789" value="{{ old('no_hp', $personel->no_hp) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $personel->email) }}">
                    <small class="text-muted" style="color: rgba(255, 255, 255, 0.5) !important;">Email digunakan untuk reset password dan menerima notifikasi sistem.</small>
                </div>
                <div class="form-group" style="display:flex; align-items:center; gap:10px; padding-top:26px;">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer; color:#fff; font-weight:600; font-size:0.9rem;">
                        <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $personel->status_aktif) ? 'checked' : '' }} style="accent-color:var(--primary); width:18px; height:18px;">
                        Status Aktif
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('admin.personel.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-military" style="padding: 12px 28px;">
                <i class="fa-solid fa-floppy-disk"></i> SIMPAN PERUBAHAN
            </button>
        </div>
    </form>

</div>

@php
    $rawEdu = explode("\n", str_replace("\r", "", $personel->pendidikan_lanjutan ?? ''));
    $rawYears = explode("\n", str_replace("\r", "", $personel->tahun_lulus_lanjutan ?? ''));
    $dikmilTiItems = [];
    $maxCount = max(count($rawEdu), count($rawYears));
    for ($i = 0; $i < $maxCount; $i++) {
        $e = trim($rawEdu[$i] ?? '');
        $y = trim($rawYears[$i] ?? '');
        if ($e !== '' || $y !== '') {
            $dikmilTiItems[] = ['edu' => $e, 'year' => $y];
        }
    }
    if (empty($dikmilTiItems)) {
        $dikmilTiItems[] = ['edu' => '', 'year' => ''];
    }
@endphp

@push('scripts')
<script>
const initialDikmilTiData = @json($dikmilTiItems);

function addDikmilTiRow(edu = '', year = '') {
    const container = document.getElementById('dikmil-ti-container');
    const rowDiv = document.createElement('div');
    rowDiv.className = 'dikmil-ti-row';
    rowDiv.style.cssText = 'display:flex; gap:12px; align-items:center;';

    const safeEdu = String(edu).replace(/"/g, '&quot;');
    const safeYear = String(year).replace(/"/g, '&quot;');

    rowDiv.innerHTML = `
        <div style="flex:2;">
            <input type="text" name="pendidikan_lanjutan_arr[]" class="form-control" placeholder="Nama Pendidikan (cth: Diklapa II / Seskoad)" value="${safeEdu}">
        </div>
        <div style="flex:1;">
            <input type="text" name="tahun_lulus_lanjutan_arr[]" class="form-control" placeholder="Tahun Lulus (cth: 2016)" value="${safeYear}">
        </div>
        <button type="button" onclick="removeDikmilTiRow(this)" class="btn-secondary" style="color:#f87171; border-color:rgba(248,113,113,0.3); padding:9px 14px;" title="Hapus Pendidikan">
            <i class="fa-solid fa-trash"></i>
        </button>
    `;
    container.appendChild(rowDiv);
}

function removeDikmilTiRow(btn) {
    const container = document.getElementById('dikmil-ti-container');
    if (container.children.length > 1) {
        btn.closest('.dikmil-ti-row').remove();
    } else {
        const row = container.children[0];
        row.querySelectorAll('input').forEach(input => input.value = '');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (initialDikmilTiData && initialDikmilTiData.length > 0) {
        initialDikmilTiData.forEach(item => addDikmilTiRow(item.edu, item.year));
    } else {
        addDikmilTiRow();
    }
    checkCorpsVisibility();
});

function checkCorpsVisibility() {
    const jenisInput = document.querySelector('input[name="jenis_personel"]');
    const isPns = jenisInput ? jenisInput.value === 'pns' : false;
    const kategori = document.getElementById('kategori_personel').value;
    const groupCorps = document.getElementById('group-corps');
    
    if (groupCorps) {
        const isPerwira = kategori === 'Perwira Pertama' || kategori === 'Perwira Menengah';
        if (isPns || (kategori !== '' && !isPerwira)) {
            groupCorps.style.display = 'none';
        } else {
            groupCorps.style.display = 'block';
        }
    }
}

document.getElementById('kategori_personel').addEventListener('change', function() {
    checkCorpsVisibility();
    const groupCorps = document.getElementById('group-corps');
    if (groupCorps && groupCorps.style.display === 'none') {
        document.getElementById('corps').value = '';
    }
});
</script>
@endpush
@endsection
