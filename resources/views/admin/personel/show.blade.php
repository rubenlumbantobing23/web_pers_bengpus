@extends('layouts.admin')

@section('page-title', 'Detail Personel — ' . $personel->nama)

@section('admin-content')
<div style="max-width: 960px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- Top Bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.4rem; color: #fff;">Detail Profil Personel</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">
                {{ $personel->jenis_personel === 'militer' ? 'Personel Militer (TNI)' : 'Pegawai Negeri Sipil' }}
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.personel.edit', $personel->id) }}" class="btn-military">
                <i class="fa-solid fa-pen-to-square"></i> Edit Data
            </a>
            <button type="button" onclick="confirmDelete('{{ $personel->id }}', '{{ addslashes($personel->nama) }}')"
                    class="btn-secondary" style="color:#f87171;">
                <i class="fa-solid fa-trash"></i> Hapus
            </button>
            <a href="{{ route('admin.personel.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Alert Head --}}
    <div class="glass-card" style="display: flex; align-items: center; gap: 20px; padding: 20px 24px;">
        <div style="width: 72px; height: 72px; border-radius: 50%; display:flex; align-items:center; justify-content:center; font-size: 2rem; font-weight: 800; color:#fff; flex-shrink:0;
            background: {{ $personel->jenis_personel === 'militer' ? 'linear-gradient(135deg, #1d4ed8, #3b82f6)' : 'linear-gradient(135deg, #92400e, #d97706)' }};">
            {{ strtoupper(substr($personel->nama, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <h3 style="font-size: 1.4rem; color: #fff; margin-bottom: 4px;">{{ $personel->nama }}</h3>
            <div style="color: var(--accent-gold); font-weight: 600; font-size: 0.95rem;">
                {{ $personel->pangkat_golongan }} &mdash; {{ $personel->jabatan }}
            </div>
            <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 2px;">
                {{ $personel->satuan_bagian }}
            </div>
        </div>
        <div>
            @if($personel->jenis_personel === 'militer')
                <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(96,165,250,0.12);color:#60a5fa;border:1px solid rgba(96,165,250,0.3);border-radius:8px;padding:6px 14px;font-size:0.85rem;font-weight:700;">
                    <i class="fa-solid fa-shield-halved"></i> MILITER
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(251,191,36,0.12);color:#fbbf24;border:1px solid rgba(251,191,36,0.3);border-radius:8px;padding:6px 14px;font-size:0.85rem;font-weight:700;">
                    <i class="fa-solid fa-briefcase"></i> PNS
                </span>
            @endif
        </div>
    </div>

    {{-- Grid Detail --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

        {{-- IDENTITAS --}}
        <div class="glass-card">
            <h4 style="color: var(--accent-gold); font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 18px; text-transform: uppercase; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-id-card"></i> IDENTITAS
            </h4>
            @php
                $fields = [
                    ['label' => $personel->label_nomor_induk, 'value' => $personel->nrp_nip],
                    ['label' => 'Jenis Kelamin',              'value' => $personel->jenis_kelamin],
                    ['label' => 'Agama',                      'value' => $personel->agama],
                    ['label' => 'Suku',                       'value' => $personel->suku],
                    ['label' => 'Tempat Lahir',               'value' => $personel->tempat_lahir],
                    ['label' => 'Tanggal Lahir',              'value' => $personel->tgl_lahir ? $personel->tgl_lahir->format('d F Y') : null],
                    ['label' => 'Status Pernikahan',          'value' => $personel->status_pernikahan],
                ];
            @endphp
            @foreach($fields as $f)
                <div style="display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="font-size:0.83rem; color:var(--text-muted);">{{ $f['label'] }}</span>
                    <span style="font-size:0.88rem; font-weight:600; color:#fff; text-align:right; max-width:60%;">{{ $f['value'] ?? '-' }}</span>
                </div>
            @endforeach
        </div>

        {{-- KEPANGKATAN --}}
        <div class="glass-card">
            <h4 style="color: var(--accent-gold); font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 18px; text-transform: uppercase; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-star"></i> KEPANGKATAN / KEPEGAWAIAN
            </h4>
            @php
                $fields2 = [
                    ['label' => 'Pangkat / Golongan', 'value' => $personel->pangkat_golongan],
                    ['label' => 'Corps',               'value' => $personel->corps],
                    ['label' => 'TMT Pangkat',         'value' => $personel->tmt_pangkat],
                    ['label' => 'Jabatan',             'value' => $personel->jabatan],
                    ['label' => 'TMT Jabatan',         'value' => $personel->tmt_jabatan],
                    ['label' => 'Bagian / Unit',       'value' => $personel->organizationUnit ? $personel->organizationUnit->name : '-'],
                    ['label' => 'Satuan',              'value' => $personel->satuan_bagian],
                    ['label' => 'TMT TNI/PNS',         'value' => $personel->tmt_tni_pa],
                    ['label' => 'MKG',                 'value' => $personel->mkg],
                ];
            @endphp
            @foreach($fields2 as $f)
                <div style="display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="font-size:0.83rem; color:var(--text-muted);">{{ $f['label'] }}</span>
                    <span style="font-size:0.88rem; font-weight:600; color:#fff; text-align:right; max-width:60%;">{{ $f['value'] ?? '-' }}</span>
                </div>
            @endforeach
        </div>

        {{-- PENDIDIKAN --}}
        <div class="glass-card">
            <h4 style="color: var(--accent-gold); font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 18px; text-transform: uppercase; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-graduation-cap"></i> PENDIDIKAN
            </h4>
            @php
                $fields3 = [
                    ['label' => 'Pendidikan Umum (DIKUM)', 'value' => $personel->dikum_ti],
                    ['label' => 'Tahun Lulus DIKUM',       'value' => $personel->thn_lulus_dikum],
                ];
                if ($personel->jenis_personel === 'militer') {
                    $fields3[] = ['label' => 'DIK PERTAMA TNI',            'value' => $personel->dikmit_tni];
                    $fields3[] = ['label' => 'TAHUN LULUS DIK PERTAMA TNI', 'value' => $personel->thn_lulus_dikmit];
                }
                $fields3[] = ['label' => 'DIKMIL TI',            'value' => $personel->pendidikan_lanjutan];
                $fields3[] = ['label' => 'TAHUN LULUS DIKMIL TI', 'value' => $personel->tahun_lulus_lanjutan];
            @endphp
            @foreach($fields3 as $f)
                <div style="display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="font-size:0.83rem; color:var(--text-muted);">{{ $f['label'] }}</span>
                    <span style="font-size:0.88rem; font-weight:600; color:#fff; text-align:right; max-width:60%;">{!! nl2br(e($f['value'] ?: '-')) !!}</span>
                </div>
            @endforeach
        </div>

        {{-- KETERANGAN & AKUN --}}
        <div class="glass-card">
            <h4 style="color: var(--accent-gold); font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 18px; text-transform: uppercase; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-note-sticky"></i> KETERANGAN & AKUN
            </h4>
            <div style="padding:9px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:0.83rem; color:var(--text-muted); margin-bottom:6px;">Keterangan</div>
                <div style="font-size:0.88rem; color:#fff;">{{ $personel->ket ?? '-' }}</div>
            </div>
            <div style="padding:9px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:0.83rem; color:var(--text-muted); margin-bottom:6px;">Nomor HP / WA</div>
                <div style="font-size:0.88rem; color:#fff;">{{ $personel->no_hp ?? '-' }}</div>
            </div>
            <div style="padding:9px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:0.83rem; color:var(--text-muted); margin-bottom:6px;">Akun Login Web</div>
                <div style="margin-top:4px;">
                    @if($personel->user)
                        <span style="color:#34d399; font-size:0.88rem;"><i class="fa-solid fa-circle-check"></i> {{ $personel->user->email }}</span>
                    @else
                        <span style="color:var(--text-muted); font-size:0.88rem;"><i class="fa-solid fa-circle-xmark"></i> Belum ada akun</span>
                    @endif
                </div>
            </div>
            <div style="padding:9px 0;">
                <div style="font-size:0.83rem; color:var(--text-muted); margin-bottom:6px;">Status</div>
                @if($personel->status_aktif)
                    <span class="badge badge-approved">AKTIF</span>
                @else
                    <span class="badge badge-rejected">NON-AKTIF</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Riwayat Cuti --}}
    @if($personel->user && $personel->user->leaveRequests->isNotEmpty())
        <div class="glass-card">
            <h4 style="color:#fff; font-size:1.05rem; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-calendar-check" style="color:#34d399;"></i> Riwayat Pengajuan Cuti
            </h4>
            <div class="table-responsive">
                <table class="custom-table" style="font-size:0.85rem;">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Cuti</th>
                            <th>Hari Kerja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personel->user->leaveRequests as $lr)
                            <tr>
                                <td><strong style="color:#fff;">{{ $lr->request_number }}</strong></td>
                                <td>{{ $lr->leaveType->name ?? '-' }}</td>
                                <td>{{ $lr->start_date->format('d/m/Y') }} – {{ $lr->end_date->format('d/m/Y') }}</td>
                                <td><strong style="color:#34d399;">{{ $lr->working_days_count }} Hari</strong></td>
                                <td>
                                    @if($lr->status === 'pending')
                                        <span class="badge badge-pending">PENDING</span>
                                    @elseif($lr->status === 'approved')
                                        <span class="badge badge-approved">DISETUJUI</span>
                                    @else
                                        <span class="badge badge-rejected">{{ strtoupper($lr->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- Delete Modal --}}
<div id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center;">
    <div class="glass-card" style="max-width:440px; width:90%; padding:28px;">
        <h3 style="color:#f87171; margin-bottom:12px;"><i class="fa-solid fa-triangle-exclamation"></i> Konfirmasi Hapus</h3>
        <p style="color:#fff; margin-bottom:6px;">Anda akan menghapus data personel:</p>
        <p id="deleteNama" style="color:var(--accent-gold); font-weight:700; font-size:1.1rem; margin-bottom:24px;"></p>
        <form id="deleteForm" method="POST">
            @csrf @method('DELETE')
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="closeDelete()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-secondary" style="background:rgba(239,68,68,0.15);border-color:rgba(239,68,68,0.4);color:#f87171;">
                    <i class="fa-solid fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id, nama) {
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteForm').action = '/admin/personel/' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDelete() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>
@endpush
@endsection


@section('admin-content')
<div style="max-width: 850px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Profil Personel: {{ $personel->nama }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">NRP/NIP: {{ $personel->nrp_nip }} | Pangkat: {{ $personel->pangkat_golongan }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.personel.edit', $personel->id) }}" class="btn-military">
                <i class="fa-solid fa-pen-to-square"></i> Edit Data
            </a>
            <a href="{{ route('admin.personel.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Personel Details Card -->
    <div class="glass-card">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #059669, #047857); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: 800;">
                {{ strtoupper(substr($personel->nama, 0, 1)) }}
            </div>
            <div>
                <h3 style="font-size: 1.4rem; color: #fff;">{{ $personel->nama }}</h3>
                <div style="font-size: 0.9rem; color: var(--accent-gold); font-weight: 600;">
                    {{ $personel->pangkat_golongan }} — {{ $personel->jabatan }}
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">NRP / NIP</span>
                <div style="font-size: 1rem; font-weight: 700; color: #fff;">{{ $personel->nrp_nip }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Satuan / Bagian</span>
                <div style="font-size: 1rem; font-weight: 600; color: #fff;">{{ $personel->satuan_bagian }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Nomor Telepon / WA</span>
                <div style="font-size: 1rem; font-weight: 600; color: #fff;">{{ $personel->no_hp ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Email Account</span>
                <div style="font-size: 1rem; font-weight: 600; color: #fff;">{{ $personel->email ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Status Keaktifan</span>
                <div style="margin-top: 4px;">
                    @if($personel->status_aktif)
                        <span class="badge badge-approved">AKTIF</span>
                    @else
                        <span class="badge badge-rejected">NON-AKTIF</span>
                    @endif
                </div>
            </div>

            <div>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Status Akun Login</span>
                <div style="margin-top: 4px;">
                    @if($personel->user)
                        <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                            <i class="fa-solid fa-user-check"></i> TERHUBUNG AKUN USER
                        </span>
                    @else
                        <span class="badge badge-cancelled">TIDAK ADA AKUN</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- History of Submissions -->
    @if($personel->user)
        <div class="glass-card">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 16px;">Riwayat Pengajuan Cuti Personel Ini</h3>
            
            @if($personel->user->leaveRequests->isEmpty())
                <p style="color: var(--text-muted); font-size: 0.9rem;">Belum pernah mengajukan cuti.</p>
            @else
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Jenis</th>
                                <th>Tanggal Cuti</th>
                                <th>Hari Kerja</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personel->user->leaveRequests as $lr)
                                <tr>
                                    <td><strong style="color: #fff;">{{ $lr->request_number }}</strong></td>
                                    <td>{{ $lr->leaveType->name ?? '-' }}</td>
                                    <td>{{ $lr->start_date->format('d/m/Y') }} - {{ $lr->end_date->format('d/m/Y') }}</td>
                                    <td><strong style="color: #34d399;">{{ $lr->working_days_count }} Hari</strong></td>
                                    <td>
                                        @if($lr->status === 'pending')
                                            <span class="badge badge-pending">PENDING</span>
                                        @elseif($lr->status === 'approved')
                                            <span class="badge badge-approved">DISETUJUI</span>
                                        @else
                                            <span class="badge badge-rejected">{{ strtoupper($lr->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
