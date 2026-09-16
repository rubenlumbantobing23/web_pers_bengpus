@extends('layouts.admin')

@section('page-title', 'Master Pejabat Penandatangan')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-pen-nib" style="color: var(--accent-gold);"></i>
                Master Pejabat Penandatangan
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Tentukan pejabat berwenang tingkat pimpinan untuk penandatanganan surat permohonan cuti, nikah, dan berkas dinas.
            </p>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="glass-card" style="padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
            <h3 style="font-size: 1.15rem; color: #fff; margin: 0;">Pengaturan Pejabat Aktif Markas</h3>
            <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                <i class="fa-solid fa-crown"></i> Pejabat Tingkat Pusat
            </span>
        </div>

        <form action="{{ route('admin.signers.update') }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $roles = [
                    'kabeng' => [
                        'label' => 'Kepala Bengpuskomlekad (Kabeng)',
                        'icon' => 'fa-crown',
                        'desc' => 'Pejabat penandatangan utama surat izin & cuti'
                    ],
                    'wakabeng' => [
                        'label' => 'Wakabeng / Wakil Kepala',
                        'icon' => 'fa-shield-halved',
                        'desc' => 'Wakil pimpinan pengesahan dokumen'
                    ],
                    'kabag' => [
                        'label' => 'Kepala Bagian (Kabag)',
                        'icon' => 'fa-user-gear',
                        'desc' => 'Atasan struktural pelaksana teknis'
                    ],
                    'kabagum' => [
                        'label' => 'Kepala Bagian Umum (Kabagum)',
                        'icon' => 'fa-user-tie',
                        'desc' => 'Pengawas tata usaha, logistik & personalia'
                    ],
                ];
            @endphp

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 20px; margin-bottom: 24px;">
                @foreach($roles as $role => $meta)
                    <div class="glass-card" style="background: rgba(9, 13, 24, 0.6); padding: 20px; border-radius: 14px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: var(--accent-gold); font-size: 1.1rem;">
                                <i class="fa-solid {{ $meta['icon'] }}"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 1rem; color: #fff; margin: 0;">{{ $meta['label'] }}</h4>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $meta['desc'] }}</span>
                            </div>
                        </div>

                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 0.82rem;">Pejabat Terpilih (Perwira ke Atas)</label>
                            <select name="signers[{{ $role }}]" class="form-control" style="font-size: 0.875rem; padding: 10px;">
                                <option value="">-- Kosongkan / Belum Ditetapkan --</option>
                                @foreach($personels as $p)
                                    <option value="{{ $p->id }}" {{ isset($signers[$role]) && $signers[$role]->personel_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} — {{ $p->pangkat_golongan }} ({{ $p->nrp_nip }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('admin.organization.index') }}" class="btn-secondary">
                    <i class="fa-solid fa-sitemap"></i> Buka Struktur & Pejabat
                </a>
                <button type="submit" class="btn-military">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pejabat Penandatangan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
