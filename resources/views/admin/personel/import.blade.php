@extends('layouts.admin')

@section('page-title', 'Import Data Personel')

@section('admin-content')
<div style="max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- Top Bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Import Massal Data Personel</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Unggah file Excel (.xls / .xlsx) untuk menambahkan banyak personel sekaligus</p>
        </div>
        <a href="{{ route('admin.personel.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Import Result --}}
    @if(session('import_success') !== null || session('import_errors'))
        <div class="glass-card" style="border-color: rgba(16,185,129,0.4);">
            <h3 style="color:#fff; font-size:1.05rem; margin-bottom:14px; display:flex; align-items:center; gap:10px;">
                <i class="fa-solid fa-chart-bar" style="color:#34d399;"></i> Hasil Import
            </h3>
            <div style="display:flex; gap:16px; margin-bottom:16px; flex-wrap:wrap;">
                <div style="flex:1; min-width:130px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:10px; padding:14px; text-align:center;">
                    <div style="font-size:2rem; font-weight:800; color:#34d399;">{{ session('import_success', 0) }}</div>
                    <div style="font-size:0.8rem; color:var(--text-muted);">Berhasil Ditambahkan</div>
                </div>
                <div style="flex:1; min-width:130px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:10px; padding:14px; text-align:center;">
                    <div style="font-size:2rem; font-weight:800; color:#f87171;">{{ count(session('import_errors', [])) }}</div>
                    <div style="font-size:0.8rem; color:var(--text-muted);">Error / Gagal</div>
                </div>
            </div>
            @if(count(session('import_errors', [])) > 0)
                <div style="background:rgba(239,68,68,0.07); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:14px; max-height:200px; overflow-y:auto;">
                    <strong style="color:#f87171; font-size:0.85rem;">Catatan Error:</strong>
                    <ul style="margin-top:8px; padding-left:18px; font-size:0.83rem; color:#fca5a5;">
                        @foreach(session('import_errors', []) as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Upload Form --}}
    <div class="glass-card">
        <h3 style="font-size:1.05rem; color:#fff; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
            <i class="fa-solid fa-file-arrow-up" style="color:#34d399;"></i> Upload File Data Personel
        </h3>

        <form action="{{ route('admin.personel.import_preview') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf

            {{-- Pilih Jenis --}}
            <div class="form-group" style="margin-bottom: 22px;">
                <label class="form-label">Jenis Personel dalam File</label>
                <div style="display: flex; gap: 14px;">
                    <label id="label-militer" style="flex:1; display:flex; align-items:center; gap:10px; cursor:pointer; padding:14px 18px; border-radius:10px; border:2px solid rgba(96,165,250,0.5); background:rgba(96,165,250,0.1); font-weight:700; color:#60a5fa; transition:all 0.2s;">
                        <input type="radio" name="jenis" value="militer" checked style="accent-color:#60a5fa; width:18px; height:18px;">
                        <i class="fa-solid fa-shield-halved" style="font-size:1.2rem;"></i>
                        <span>Militer (TNI)</span>
                    </label>
                    <label id="label-pns" style="flex:1; display:flex; align-items:center; gap:10px; cursor:pointer; padding:14px 18px; border-radius:10px; border:2px solid rgba(251,191,36,0.3); background:rgba(251,191,36,0.05); font-weight:700; color:var(--text-muted); transition:all 0.2s;">
                        <input type="radio" name="jenis" value="pns" style="accent-color:#fbbf24; width:18px; height:18px;">
                        <i class="fa-solid fa-briefcase" style="font-size:1.2rem;"></i>
                        <span>PNS / Sipil</span>
                    </label>
                </div>
            </div>

            {{-- Drag & Drop --}}
            <div id="dropzone"
                onclick="document.getElementById('file-input').click()"
                ondragover="event.preventDefault(); this.classList.add('dragover')"
                ondragleave="this.classList.remove('dragover')"
                ondrop="onFileDrop(event)"
                style="border:2px dashed rgba(52,211,153,0.4); border-radius:14px; padding:50px 24px; text-align:center; cursor:pointer; transition:all 0.2s; background:rgba(52,211,153,0.03); margin-bottom:20px;">
                <div id="dropzone-icon" style="font-size:2.8rem; color:#34d399; margin-bottom:12px; opacity:0.7;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div style="font-size:1rem; font-weight:600; color:#fff;" id="dropzone-text">Klik atau Seret File ke Sini</div>
                <div style="font-size:0.85rem; color:var(--text-muted); margin-top:6px;">Format: XLS, XLSX — Maks. 10MB</div>
                <input type="file" id="file-input" name="file" accept=".xlsx,.xls" style="display:none;" onchange="onFileSelected(this)">
            </div>

            @error('file')
                <div style="color:#f87171; font-size:0.875rem; margin-bottom:14px;"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
            @error('jenis')
                <div style="color:#f87171; font-size:0.875rem; margin-bottom:14px;"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror

            <div style="background:rgba(96,165,250,0.08); border:1px solid rgba(96,165,250,0.2); border-radius:10px; padding:12px 16px; font-size:0.83rem; color:#93c5fd; margin-bottom:20px;">
                <i class="fa-solid fa-circle-info" style="margin-right:6px;"></i>
                <strong>Format kolom Excel (22 kolom):</strong>
                NO | NAMA | PANGKAT | TMT PANGKAT | CORPS | NRP/NIP | JABATAN | TMT JABATAN | SATUAN | TMT TNI/PNS |
                SUKU | AGAMA | TGL LAHIR | TEMPAT LAHIR | MKG | JENIS KELAMIN | DIKUM | THN LULUS |
                DIK PERTAMA TNI | TAHUN LULUS DIK PERTAMA TNI | DIKMIL TI | KETERANGAN
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" id="btn-import" class="btn-military" style="padding:12px 28px;" disabled>
                    <i class="fa-solid fa-eye"></i> Preview & Validasi Data
                </button>
            </div>
        </form>
    </div>

</div>

<style>
#dropzone.dragover { border-color: rgba(52,211,153,0.8); background: rgba(52,211,153,0.08); }
</style>

@push('scripts')
<script>
function onFileSelected(input) {
    const file = input.files[0];
    if (!file) return;
    updateDropzone(file.name);
}
function onFileDrop(e) {
    e.preventDefault();
    document.getElementById('dropzone').classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (!file) return;
    document.getElementById('file-input').files = e.dataTransfer.files;
    updateDropzone(file.name);
}
function updateDropzone(filename) {
    document.getElementById('dropzone-icon').innerHTML = '<i class="fa-solid fa-file-excel" style="color:#34d399;"></i>';
    document.getElementById('dropzone-text').textContent = '📄 ' + filename + ' — Siap dipreview';
    document.getElementById('btn-import').disabled = false;
}
</script>
@endpush
@endsection


@section('admin-content')
<div style="max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
