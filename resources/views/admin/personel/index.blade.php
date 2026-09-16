@extends('layouts.admin')

@section('page-title', 'Nominatif Personel')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px; min-width: 0; max-width: 100%;">

    @push('scripts')
    <style>
        .table-container {
            overflow-x: auto;
            width: 100%;
            max-width: 100%;
        }
        
        /* Pagination Styles */
        .pagination {
            display: flex;
            list-style: none;
            padding-left: 0;
            gap: 5px;
            margin: 0;
        }
        .page-item .page-link {
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-item .page-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .page-item.active .page-link {
            background: var(--accent-gold);
            color: #111;
            border-color: var(--accent-gold);
            font-weight: 600;
        }
        .page-item.disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }
        .pagination svg {
            width: 16px;
            height: 16px;
        }
        p.small.text-muted {
            display: none; /* Hide default trailing texts if any */
        }
        .custom-table th.sticky-col-no, 
        .custom-table td.sticky-col-no {
            position: sticky;
            left: 0;
            background-color: #1e293b; /* Solid background to prevent overlap visibility */
            z-index: 2;
        }
        .custom-table th.sticky-col-nama, 
        .custom-table td.sticky-col-nama {
            position: sticky;
            left: 50px; /* Width of NO column roughly */
            background-color: #1e293b;
            z-index: 2;
            border-right: 2px solid var(--border-color);
        }
        .custom-table th.sticky-col-no,
        .custom-table th.sticky-col-nama {
            z-index: 3; /* Header stays above rows */
        }
    </style>
    @endpush

    {{-- Header --}}
    <div style="text-align: center; padding: 8px 0 4px;">
        <h2 style="font-size: 1.6rem; color: #fff; font-weight: 800; letter-spacing: 1px;">NOMINATIF PERSONEL</h2>
        <p style="color: var(--accent-gold); font-size: 0.95rem; font-weight: 600;">BENGKEL PUSAT KOMUNIKASI DAN ELEKTRONIKA</p>
        @if($lastUpdated)
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px;">
                <i class="fa-solid fa-clock"></i> Terakhir diperbarui: {{ $lastUpdated->format('d F Y H:i') }}
            </p>
        @endif
    </div>

    {{-- Top Action Bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.personel.import_form') }}" class="btn-secondary">
                <i class="fa-solid fa-file-arrow-up"></i> Import Excel
            </a>
            <a href="{{ route('admin.personel.export', array_merge(request()->only(['search']), ['jenis' => request('jenis', 'semua')])) }}" class="btn-secondary">
                <i class="fa-solid fa-file-arrow-down"></i> Export Excel
            </a>
        </div>
        <a href="{{ route('admin.personel.create') }}" class="btn-military">
            <i class="fa-solid fa-user-plus"></i> Tambah Personel
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(52,211,153,.25), rgba(16,185,129,.1)); display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-users" style="color:#34d399; font-size:1.3rem;"></i>
            </div>
            <div>
                <div style="font-size:1.8rem; font-weight:800; color:#fff;">{{ $totalAll }}</div>
                <div style="font-size:0.775rem; color:var(--text-muted);">Total Personel</div>
            </div>
        </div>
        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(96,165,250,.25), rgba(59,130,246,.1)); display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-shield-halved" style="color:#60a5fa; font-size:1.3rem;"></i>
            </div>
            <div>
                <div style="font-size:1.8rem; font-weight:800; color:#fff;">{{ $totalMiliter }}</div>
                <div style="font-size:0.775rem; color:var(--text-muted);">Personel Militer</div>
            </div>
        </div>
        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(251,191,36,.25), rgba(245,158,11,.1)); display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-briefcase" style="color:#fbbf24; font-size:1.3rem;"></i>
            </div>
            <div>
                <div style="font-size:1.8rem; font-weight:800; color:#fff;">{{ $totalPns }}</div>
                <div style="font-size:0.775rem; color:var(--text-muted);">Personel PNS</div>
            </div>
        </div>
    </div>

    {{-- Search & Per Page --}}
    <div class="glass-card" style="padding: 16px;">
        <form action="{{ route('admin.personel.index') }}" method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:2; min-width:200px;">
                <label class="form-label" for="search">Cari Nama / NRP / NIP / Pangkat / Jabatan</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
            </div>
            <div style="min-width:130px;">
                <label class="form-label" for="jenis">Jenis Personel</label>
                <select id="jenis" name="jenis" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="militer" {{ request('jenis') === 'militer' ? 'selected' : '' }}>🎖 Militer</option>
                    <option value="pns" {{ request('jenis') === 'pns' ? 'selected' : '' }}>💼 PNS</option>
                </select>
            </div>
            <div style="min-width:130px;">
                <label class="form-label" for="per_page">Per Halaman</label>
                <select id="per_page" name="per_page" class="form-control" onchange="this.form.submit()">
                    @foreach([10, 25, 50, 100] as $pp)
                        <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }} data</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn-military"><i class="fa-solid fa-search"></i> Cari</button>
                @if(request()->hasAny(['search','jenis','per_page']))
                    <a href="{{ route('admin.personel.index') }}" class="btn-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Nominatif --}}
    <div class="glass-card" style="padding:0; overflow:hidden; min-width:0; width:100%;">
        @if($personels->isEmpty())
            <div style="text-align:center; padding:60px 20px; color:var(--text-muted);">
                <i class="fa-solid fa-users-slash" style="font-size:2.8rem; margin-bottom:14px; opacity:0.4;"></i>
                <p style="font-size:1rem;">Tidak ada data personel yang ditemukan.</p>
                <a href="{{ route('admin.personel.create') }}" class="btn-military" style="margin-top:14px; display:inline-block;">
                    <i class="fa-solid fa-user-plus"></i> Tambah Personel
                </a>
            </div>
        @else
            <div class="table-container">
                <table class="custom-table" style="min-width:2500px; font-size:0.85rem; white-space: nowrap;">
                    <thead>
                        <tr>
                            <th class="sticky-col-no" style="width:50px; text-align:center;">NO</th>
                            <th class="sticky-col-nama">NAMA LENGKAP</th>
                            <th>STATUS AKUN</th>
                            <th>{{ request('jenis') === 'militer' ? 'PANGKAT' : (request('jenis') === 'pns' ? 'GOLONGAN' : 'PANGKAT / GOLONGAN') }}</th>
                            <th>TMT PANGKAT</th>
                            @if(request('jenis') !== 'pns')
                            <th>CORPS</th>
                            @endif
                            <th>{{ request('jenis') === 'militer' ? 'NRP' : (request('jenis') === 'pns' ? 'NIP' : 'NRP / NIP') }}</th>
                            <th>JABATAN</th>
                            <th>TMT JABATAN</th>
                            <th>SATUAN</th>
                            <th>{{ request('jenis') === 'militer' ? 'TMT TNI' : (request('jenis') === 'pns' ? 'TMT PNS' : 'TMT TNI / PNS') }}</th>
                            <th>SUKU / AGAMA</th>
                            <th>TGL LAHIR</th>
                            <th>TEMPAT LAHIR</th>
                            <th>MKG</th>
                            <th>JENIS KELAMIN</th>
                            <th>STATUS PERNIKAHAN</th>
                            <th>DIKUM / PENDIDIKAN</th>
                            <th>THN LULUS DIKUM</th>
                            @if(request('jenis') !== 'pns')
                            <th>DIK PERTAMA TNI</th>
                            <th>TAHUN LULUS DIK PERTAMA TNI</th>
                            @endif
                            <th>DIKMIL TI</th>
                            <th>TAHUN LULUS DIKMIL TI</th>
                            <th>KETERANGAN</th>
                            <th style="position: sticky; right: 0; background: #1e293b; z-index: 3; border-left: 2px solid var(--border-color); text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personels as $i => $p)
                        <tr>
                            <td class="sticky-col-no" style="text-align:center; color:var(--text-muted);">
                                {{ ($personels->currentPage()-1) * $personels->perPage() + $i + 1 }}
                            </td>
                            <td class="sticky-col-nama"><strong style="color:#fff;">{{ $p->nama }}</strong></td>
                            <td>
                                @if($p->user_id)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; font-weight: 700; padding: 4px 10px;">
                                        <i class="fa-solid fa-circle-check"></i> Sudah Memiliki Akun
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171; font-weight: 600; padding: 4px 10px;">
                                        <i class="fa-solid fa-circle-xmark"></i> Belum Memiliki Akun
                                    </span>
                                @endif
                            </td>
                            <td style="color:var(--accent-gold); font-weight:600;">{{ $p->pangkat_golongan ?? '-' }}</td>
                            <td>{!! nl2br(e($p->tmt_pangkat ?? '-')) !!}</td>
                            @if(request('jenis') !== 'pns')
                            <td>{{ $p->corps ?? '-' }}</td>
                            @endif
                            <td><strong style="color:var(--accent-gold);">{{ $p->nrp_nip }}</strong></td>
                            <td>{{ $p->jabatan ?? '-' }}</td>
                            <td>{{ $p->tmt_jabatan ?? '-' }}</td>
                            <td>
                                @if($p->organizationUnit)
                                    <span class="badge bg-info">{{ $p->organizationUnit->name }}</span>
                                @else
                                    {{ $p->satuan_bagian ?? '-' }}
                                @endif
                            </td>
                            <td>{!! nl2br(e($p->tmt_tni_pa ?? '-')) !!}</td>
                            <td>{!! nl2br(e($p->agama_suku ?: '-')) !!}</td>
                            <td>{{ $p->tgl_lahir ? \Carbon\Carbon::parse($p->tgl_lahir)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $p->tempat_lahir ?? '-' }}</td>
                            <td>{{ $p->mkg ?? '-' }}</td>
                            <td>{{ $p->jenis_kelamin ?? '-' }}</td>
                            <td>{{ $p->status_pernikahan ?? '-' }}</td>
                            <td>{!! nl2br(e($p->dikum_ti ?: '-')) !!}</td>
                            <td>{!! nl2br(e($p->thn_lulus_dikum ?: '-')) !!}</td>
                            @if(request('jenis') !== 'pns')
                            <td><span style="color:#60a5fa;">{!! nl2br(e($p->dikmit_tni ?: '-')) !!}</span></td>
                            <td>{!! nl2br(e($p->thn_lulus_dikmit ?: '-')) !!}</td>
                            @endif
                            <td>{!! nl2br(e($p->pendidikan_lanjutan ?: '-')) !!}</td>
                            <td>{!! nl2br(e($p->tahun_lulus_lanjutan ?: '-')) !!}</td>
                            <td>{{ $p->ket ?? '-' }}</td>
                            <td style="position: sticky; right: 0; background: #1e293b; z-index: 2; border-left: 2px solid var(--border-color);">
                                <div style="display:flex; gap:4px; justify-content:center;">
                                    <a href="{{ route('admin.personel.show', $p->id) }}"
                                       class="btn-secondary" style="padding:5px 8px;font-size:0.78rem;" title="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.personel.edit', $p->id) }}"
                                       class="btn-secondary" style="padding:5px 8px;font-size:0.78rem;color:#fbbf24;" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" onclick="confirmDelete('{{ $p->id }}', '{{ addslashes($p->nama) }}')"
                                            class="btn-secondary" style="padding:5px 8px;font-size:0.78rem;color:#f87171;" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:14px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; border-top:1px solid var(--border-color);">
                <div style="font-size:0.83rem; color:var(--text-muted);">
                    Menampilkan {{ $personels->firstItem() }}–{{ $personels->lastItem() }} dari {{ $personels->total() }} data
                </div>
                {{ $personels->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center;">
    <div class="glass-card" style="max-width:440px; width:90%; padding:28px;">
        <h3 style="color:#f87171; margin-bottom:12px;"><i class="fa-solid fa-triangle-exclamation"></i> Konfirmasi Hapus</h3>
        <p style="color:#fff; margin-bottom:6px;">Anda akan menghapus data personel:</p>
        <p id="deleteNama" style="color:var(--accent-gold); font-weight:700; font-size:1.1rem; margin-bottom:16px;"></p>
        <p style="color:var(--text-muted); font-size:0.88rem; margin-bottom:24px;">
            Tindakan ini tidak dapat dibatalkan.
        </p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
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
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDelete();
});
</script>
@endpush
@endsection
