@extends('layouts.admin')

@section('page-title', 'Kelola Kalender / Hari Libur')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengelolaan Kalender & Hari Libur Nasional</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola daftar tanggal libur nasional & cuti bersama yang dikecualikan dari jatah cuti anggota</p>
        </div>

        <button type="button" class="btn-military" onclick="openAddHolidayModal()">
            <i class="fa-solid fa-calendar-plus"></i> Tambah Hari Libur Baru
        </button>
    </div>

    <!-- Year Filter Bar -->
    <div class="glass-card" style="padding: 16px 20px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-weight: 700; color: #fff;">Tahun Kalender:</span>
                <select class="form-control" style="width: 140px; padding: 6px 12px;" onchange="location.href='?year=' + this.value">
                    <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                    <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                    <option value="2027" {{ $year == 2027 ? 'selected' : '' }}>2027</option>
                </select>
            </div>
            <div style="font-size: 0.875rem; color: var(--text-muted);">
                Total Libur Terdaftar: <strong style="color: #34d399;">{{ $holidays->count() }} Hari</strong>
            </div>
        </div>
    </div>

    <!-- Holidays List Table -->
    <div class="glass-card">
        @if($holidays->isEmpty())
            <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4;"></i>
                <p>Belum ada data hari libur yang didaftarkan untuk tahun {{ $year }}.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Tanggal Libur</th>
                            <th>Nama Keterangan Libur</th>
                            <th>Kategori / Tipe</th>
                            <th>Tahun</th>
                            <th>Aksi Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holidays as $h)
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 0.95rem;">
                                        {{ \Carbon\Carbon::parse($h->date)->format('d F Y') }}
                                    </strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ \Carbon\Carbon::parse($h->date)->isoFormat('dddd') }}
                                    </div>
                                </td>
                                <td><strong style="color: #34d399;">{{ $h->name }}</strong></td>
                                <td>
                                    @if($h->type === 'national_holiday')
                                        <span class="badge badge-rejected" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">LIBUR NASIONAL</span>
                                    @else
                                        <span class="badge badge-pending" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">CUTI BERSAMA</span>
                                    @endif
                                </td>
                                <td>{{ $h->year }}</td>
                                <td>
                                    <form action="{{ route('admin.holidays.destroy', $h->id) }}" method="POST" onsubmit="return confirm('Hapus tanggal libur ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" style="padding: 6px 12px; font-size: 0.8rem;">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal Dialog Add Holiday -->
    <div id="holidayModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 200; align-items: center; justify-content: center; padding: 20px;">
        <div class="glass-card" style="width: 100%; max-width: 500px; background: var(--bg-card);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <h3 style="font-size: 1.2rem; color: #fff;">Tambah Hari Libur Baru</h3>
                <button type="button" onclick="closeHolidayModal()" style="background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.holidays.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="date">Tanggal Libur</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Nama Hari Libur</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Hari Kemerdekaan RI" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="type">Kategori Libur</label>
                    <select id="type" name="type" class="form-control">
                        <option value="national_holiday">Hari Libur Nasional</option>
                        <option value="collective_leave">Cuti Bersama Pemerintah</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn-secondary" onclick="closeHolidayModal()">Batal</button>
                    <button type="submit" class="btn-military">Simpan Tanggal Libur</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openAddHolidayModal() {
        document.getElementById('holidayModal').style.display = 'flex';
    }

    function closeHolidayModal() {
        document.getElementById('holidayModal').style.display = 'none';
    }
</script>
@endpush
@endsection
