@extends('layouts.admin')

@section('page-title', 'Konfigurasi Struktur & Pejabat')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-diagram-project" style="color: var(--accent-gold);"></i>
                Konfigurasi Struktur & Pejabat
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">
                Kelola hierarki organisasi (Unit, Bagian, Seksi, Subbengkel) serta penugasan pejabat penanggung jawab secara dinamis.
            </p>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button type="button" class="btn-secondary" onclick="openModal('modalTambahUnit')">
                <i class="fa-solid fa-folder-plus"></i> Tambah Unit / Subunit
            </button>
            <button type="button" class="btn-military" onclick="openAssignModal()">
                <i class="fa-solid fa-user-tag"></i> Tugaskan Pejabat
            </button>
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

    @if(isset($errors) && $errors->any())
        <div class="alert alert-error" style="align-items: flex-start;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top: 2px;"></i>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Metrics Summary Cards -->
    @php
        $activeAssignmentsCount = $history->where('is_active', true)->count();
        $totalUnitsCount = $allUnits->count();
    @endphp
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(96, 165, 250, 0.25), rgba(59, 130, 246, 0.1)); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                <i class="fa-solid fa-sitemap" style="color: #60a5fa;"></i>
            </div>
            <div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #fff;">{{ $totalUnitsCount }}</div>
                <div style="font-size: 0.775rem; color: var(--text-muted);">Total Unit / Subbagian</div>
            </div>
        </div>

        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(52, 211, 153, 0.25), rgba(16, 185, 129, 0.1)); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                <i class="fa-solid fa-user-shield" style="color: #34d399;"></i>
            </div>
            <div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #fff;">{{ $activeAssignmentsCount }}</div>
                <div style="font-size: 0.775rem; color: var(--text-muted);">Pejabat Unit Aktif</div>
            </div>
        </div>



        <div class="glass-card" style="padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(168, 85, 247, 0.25), rgba(147, 51, 234, 0.1)); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                <i class="fa-solid fa-history" style="color: #c084fc;"></i>
            </div>
            <div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #fff;">{{ $history->count() }}</div>
                <div style="font-size: 0.775rem; color: var(--text-muted);">Riwayat Penugasan</div>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; overflow-x: auto;">
        <button type="button" class="tab-btn active" id="btn-tab-struktur" onclick="switchTab('struktur')">
            <i class="fa-solid fa-network-wired"></i>
            <span>Struktur Organisasi</span>
        </button>

        <button type="button" class="tab-btn" id="btn-tab-riwayat" onclick="switchTab('riwayat')">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Riwayat Perubahan ({{ $history->count() }})</span>
        </button>
    </div>

    <!-- STRUKTUR ORGANISASI (TABEL) -->
    <div id="tab-pane-struktur" class="tab-pane active">
        <div class="glass-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="font-size: 1.15rem; color: #fff; margin: 0;">Hierarki Bagian & Penugasan Pejabat (Tabel)</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 4px 0 0 0;">
                        Menampilkan susunan unit kerja 3 level (Unit Utama &rarr; Seksi/Bagian &rarr; Sub-seksi/Subbengkel).
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                        <i class="fa-solid fa-shield"></i> Unit Utama
                    </span>
                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                        <i class="fa-solid fa-diagram-next"></i> Subunit / Bagian
                    </span>
                    <span class="badge" style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);">
                        <i class="fa-solid fa-wrench"></i> Sub-seksi
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="min-width: 320px;">Unit / Subunit Kerja</th>
                            <th style="width: 110px;">Kode</th>
                            <th style="width: 110px;">Status</th>
                            <th style="min-width: 280px;">Pejabat Penanggung Jawab</th>
                            <th style="width: 180px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            <!-- LEVEL 1: UNIT UTAMA -->
                            <tr style="background: rgba(255, 255, 255, 0.035); border-top: 1px solid rgba(255, 255, 255, 0.08);">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: var(--accent-gold); font-size: 0.9rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size: 1rem; color: #fff; letter-spacing: 0.02em;">{{ $unit->name }}</strong>
                                            @if($unit->isCategoryHeader())
                                                <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">
                                                    <i class="fa-solid fa-layer-group"></i> Judul Kelompok / Header
                                                </div>
                                            @else
                                                <div style="font-size: 0.75rem; color: var(--accent-gold); font-weight: 600; text-transform: uppercase;">
                                                    Unit Utama (Tingkat 1)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(!$unit->isCategoryHeader())
                                        <code style="background: rgba(255, 255, 255, 0.06); padding: 4px 8px; border-radius: 6px; color: #cbd5e1; font-size: 0.8rem;">
                                            {{ $unit->code ?? '-' }}
                                        </code>
                                    @endif
                                </td>
                                <td>
                                    @if(!$unit->isCategoryHeader())
                                        @if($unit->is_active)
                                            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                                                <i class="fa-solid fa-check"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">
                                                Nonaktif
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!$unit->isCategoryHeader())
                                        @php $hasActive = false; @endphp
                                        @foreach($unit->assignments as $assignment)
                                            @if($assignment->is_active)
                                                @php $hasActive = true; @endphp
                                                <div class="official-pill">
                                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                                        <span class="role-badge role-gold">{{ $assignment->role_label }}</span>
                                                        <form action="{{ route('admin.organization.assignment.deactivate', $assignment->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan pejabat {{ $assignment->personel->nama }}?');">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="icon-deactivate-btn" title="Nonaktifkan Penugasan">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div style="color: #fff; font-weight: 600; font-size: 0.875rem; margin-top: 4px;">
                                                        {{ $assignment->personel->nama }}
                                                    </div>
                                                    <div style="color: var(--text-muted); font-size: 0.775rem;">
                                                        {{ $assignment->personel->pangkat_golongan }} ({{ $assignment->personel->nrp_nip }})
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        @if(!$hasActive)
                                            <span style="font-size: 0.82rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fa-regular fa-circle-dot"></i> Belum ada pejabat
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if(!$unit->isCategoryHeader())
                                        <div style="display: inline-flex; gap: 6px;">
                                            <button type="button" class="btn-action-primary" onclick="openAssignModal('{{ $unit->id }}', '{{ e($unit->name) }}')" title="Tugaskan Pejabat">
                                                <i class="fa-solid fa-user-plus"></i> Pejabat
                                            </button>
                                            <button type="button" class="btn-action-edit" onclick="openEditUnitModal('{{ $unit->id }}', '{{ e($unit->name) }}', '{{ e($unit->code) }}', '{{ $unit->parent_id }}', '{{ $unit->level }}', '{{ $unit->sort_order }}', {{ $unit->is_active ? 1 : 0 }})" title="Edit Unit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>

                            <!-- LEVEL 2: CHILDREN (SUBUNIT / BAGIAN) -->
                            @foreach($unit->children as $child)
                                <tr style="background: rgba(255, 255, 255, 0.015);">
                                    <td style="padding-left: 36px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span style="color: var(--text-muted); font-family: monospace; font-size: 1.1rem; opacity: 0.6;">└──</span>
                                            @php
                                                $isKabeng = ($child->name === 'KABENG' || $child->name === 'KEPALA');
                                                $isWakabeng = ($child->name === 'WAKABENG' || $child->name === 'WAKIL KEPALA');
                                            @endphp
                                            <div style="width: 28px; height: 28px; border-radius: 6px; background: {{ $isKabeng ? 'rgba(245, 158, 11, 0.18)' : ($isWakabeng ? 'rgba(59, 130, 246, 0.18)' : 'rgba(59, 130, 246, 0.15)') }}; border: 1px solid {{ $isKabeng ? 'rgba(245, 158, 11, 0.35)' : ($isWakabeng ? 'rgba(59, 130, 246, 0.35)' : 'rgba(59, 130, 246, 0.3)') }}; display: flex; align-items: center; justify-content: center; color: {{ $isKabeng ? '#fbbf24' : ($isWakabeng ? '#60a5fa' : '#60a5fa') }}; font-size: 0.8rem; flex-shrink: 0;">
                                                @if($isKabeng)
                                                    <i class="fa-solid fa-crown"></i>
                                                @elseif($isWakabeng)
                                                    <i class="fa-solid fa-shield-halved"></i>
                                                @else
                                                    <i class="fa-solid fa-folder-open"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <span style="font-weight: 600; color: #f1f5f9; font-size: 0.925rem;">{{ $child->name }}</span>
                                                <div style="font-size: 0.72rem; color: {{ $isKabeng ? '#fbbf24' : ($isWakabeng ? '#60a5fa' : '#60a5fa') }};">
                                                    @if($isKabeng)
                                                        Kepala Bengpuskomlekad (Pimpinan)
                                                    @elseif($isWakabeng)
                                                        Wakil Kepala Bengpuskomlekad (Pimpinan)
                                                    @else
                                                        Subunit / Seksi
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code style="background: rgba(255, 255, 255, 0.04); padding: 3px 6px; border-radius: 4px; color: #94a3b8; font-size: 0.78rem;">
                                            {{ $child->code ?? '-' }}
                                        </code>
                                    </td>
                                    <td>
                                        @if($child->is_active)
                                            <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #34d399; font-size: 0.75rem;">
                                                <i class="fa-solid fa-check"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #f87171; font-size: 0.75rem;">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $hasActiveChild = false; @endphp
                                        @foreach($child->assignments as $assignment)
                                            @if($assignment->is_active)
                                                @php $hasActiveChild = true; @endphp
                                                <div class="official-pill">
                                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                                        <span class="role-badge {{ $assignment->role === 'kabeng' ? 'role-gold' : 'role-blue' }}">{{ $assignment->role_label }}</span>
                                                        <form action="{{ route('admin.organization.assignment.deactivate', $assignment->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan pejabat {{ $assignment->personel->nama }}?');">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="icon-deactivate-btn" title="Nonaktifkan Penugasan">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div style="color: #fff; font-weight: 600; font-size: 0.85rem; margin-top: 4px;">
                                                        {{ $assignment->personel->nama }}
                                                    </div>
                                                    <div style="color: var(--text-muted); font-size: 0.75rem;">
                                                        {{ $assignment->personel->pangkat_golongan }} ({{ $assignment->personel->nrp_nip }})
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        @if(!$hasActiveChild)
                                            <span style="font-size: 0.8rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fa-regular fa-circle-dot"></i> Belum ada pejabat
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; gap: 6px;">
                                            <button type="button" class="btn-action-primary" onclick="openAssignModal('{{ $child->id }}', '{{ e($child->name) }}')" title="Tugaskan Pejabat">
                                                <i class="fa-solid fa-user-plus"></i> Pejabat
                                            </button>
                                            <button type="button" class="btn-action-edit" onclick="openEditUnitModal('{{ $child->id }}', '{{ e($child->name) }}', '{{ e($child->code) }}', '{{ $child->parent_id }}', '{{ $child->level }}', '{{ $child->sort_order }}', {{ $child->is_active ? 1 : 0 }})" title="Edit Subunit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- LEVEL 3: GRANDCHILDREN (SUB-BAGIAN / SUBBENG) -->
                                @foreach($child->children as $grandchild)
                                    <tr style="background: rgba(0, 0, 0, 0.15);">
                                        <td style="padding-left: 68px;">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <span style="color: var(--text-muted); font-family: monospace; font-size: 1.1rem; opacity: 0.4;">└──</span>
                                                <div style="width: 24px; height: 24px; border-radius: 5px; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.3); display: flex; align-items: center; justify-content: center; color: #c084fc; font-size: 0.75rem; flex-shrink: 0;">
                                                    <i class="fa-solid fa-wrench"></i>
                                                </div>
                                                <div>
                                                    <span style="font-weight: 500; color: #e2e8f0; font-size: 0.875rem;">{{ $grandchild->name }}</span>
                                                    <div style="font-size: 0.7rem; color: #c084fc;">Jabatan / Kasubbeng</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code style="background: rgba(255, 255, 255, 0.03); padding: 2px 5px; border-radius: 4px; color: #94a3b8; font-size: 0.75rem;">
                                                {{ $grandchild->code ?? '-' }}
                                            </code>
                                        </td>
                                        <td>
                                            @if($grandchild->is_active)
                                                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #34d399; font-size: 0.72rem;">
                                                    <i class="fa-solid fa-check"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #f87171; font-size: 0.72rem;">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $hasActiveGrand = false; @endphp
                                            @foreach($grandchild->assignments as $assignment)
                                                @if($assignment->is_active)
                                                    @php $hasActiveGrand = true; @endphp
                                                    <div class="official-pill">
                                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                                            <span class="role-badge role-purple">{{ $assignment->role_label }}</span>
                                                            <form action="{{ route('admin.organization.assignment.deactivate', $assignment->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan pejabat {{ $assignment->personel->nama }}?');">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="icon-deactivate-btn" title="Nonaktifkan Penugasan">
                                                                    <i class="fa-solid fa-xmark"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                        <div style="color: #fff; font-weight: 600; font-size: 0.825rem; margin-top: 4px;">
                                                            {{ $assignment->personel->nama }}
                                                        </div>
                                                        <div style="color: var(--text-muted); font-size: 0.725rem;">
                                                            {{ $assignment->personel->pangkat_golongan }} ({{ $assignment->personel->nrp_nip }})
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach

                                            @if(!$hasActiveGrand)
                                                <span style="font-size: 0.78rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px;">
                                                    <i class="fa-regular fa-circle-dot"></i> Belum ada pejabat
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: inline-flex; gap: 6px;">
                                                <button type="button" class="btn-action-primary" onclick="openAssignModal('{{ $grandchild->id }}', '{{ e($grandchild->name) }}')" title="Tugaskan Pejabat">
                                                    <i class="fa-solid fa-user-plus"></i> Pejabat
                                                </button>
                                                <button type="button" class="btn-action-edit" onclick="openEditUnitModal('{{ $grandchild->id }}', '{{ e($grandchild->name) }}', '{{ e($grandchild->code) }}', '{{ $grandchild->parent_id }}', '{{ $grandchild->level }}', '{{ $grandchild->sort_order }}', {{ $grandchild->is_active ? 1 : 0 }})" title="Edit Sub-seksi">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    <i class="fa-solid fa-sitemap" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.3;"></i>
                                    <p>Belum ada data struktur organisasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <!-- TAB 3: RIWAYAT PERUBAHAN -->
    <div id="tab-pane-riwayat" class="tab-pane" style="display: none;">
        <div class="glass-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="font-size: 1.15rem; color: #fff; margin: 0;">Log Histori Pergantian Pejabat</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 4px 0 0 0;">
                        Merekam jejak penugasan terdahulu untuk integritas data persuratan dan riwayat kedinasan.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Tanggal Penugasan</th>
                            <th>Unit / Subunit</th>
                            <th>Role / Jabatan</th>
                            <th>Nama Personel</th>
                            <th>Pangkat & NRP</th>
                            <th>Status Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                            <tr>
                                <td>
                                    <div style="font-size: 0.85rem; color: #fff; font-weight: 500;">
                                        {{ $item->created_at->format('d M Y') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ $item->created_at->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: #fff; font-size: 0.9rem;">{{ $item->unit->name ?? 'Unit Dihapus' }}</strong>
                                    @if($item->unit && $item->unit->code)
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">Kode: {{ $item->unit->code }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="role-badge role-blue">{{ $item->role_label }}</span>
                                </td>
                                <td>
                                    <strong style="color: #f1f5f9; font-size: 0.9rem;">{{ $item->personel->nama ?? '-' }}</strong>
                                </td>
                                <td>
                                    <span style="color: var(--accent-gold); font-size: 0.85rem;">{{ $item->personel->pangkat_golongan ?? '-' }}</span>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $item->personel->nrp_nip ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="fa-solid fa-circle-check"></i> AKTIF
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.25);">
                                            SELESAI / NONAKTIF
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    <i class="fa-solid fa-clock-rotate-left" style="font-size: 2rem; opacity: 0.3; margin-bottom: 8px;"></i>
                                    <p>Belum ada riwayat pergantian pejabat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ==================== MODALS ==================== -->

<!-- 1. MODAL TAMBAH UNIT / SUBUNIT -->
<div id="modalTambahUnit" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <button type="button" class="modal-close-btn" onclick="closeModal('modalTambahUnit')">&times;</button>
        
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(5, 150, 105, 0.15); border: 1px solid rgba(5, 150, 105, 0.3); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.1rem;">
                <i class="fa-solid fa-folder-plus"></i>
            </div>
            <div>
                <h3 style="font-size: 1.2rem; color: #fff; margin: 0;">Tambah Unit / Subunit</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">Buat bagian atau sub-bagian baru dalam struktur</p>
            </div>
        </div>

        <form action="{{ route('admin.organization.unit.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="add_name">Nama Unit / Subunit <span style="color: var(--accent-red);">*</span></label>
                <input type="text" id="add_name" name="name" class="form-control" placeholder="Contoh: SUBBENG MEKATRONIKA" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label" for="add_code">Kode Singkatan</label>
                    <input type="text" id="add_code" name="code" class="form-control" placeholder="Contoh: SB-MEK">
                </div>

                <div class="form-group">
                    <label class="form-label" for="add_level">Tingkat / Level <span style="color: var(--accent-red);">*</span></label>
                    <select id="add_level" name="level" class="form-control" required>
                        <option value="subunit">Subunit / Bagian</option>
                        <option value="unit">Unit Utama (Induk Atas)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="add_parent_id">Unit Induk (Parent)</label>
                <select id="add_parent_id" name="parent_id" class="form-control">
                    <option value="">-- Tidak Ada (Tingkat Paling Atas) --</option>
                    @foreach($allUnits as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 0.775rem;">
                    Pilih unit induk tempat subunit ini bernaung. Kosongkan jika unit berdiri sendiri.
                </small>
            </div>

            <input type="hidden" id="add_sort_order" name="sort_order" value="10">

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTambahUnit')">Batal</button>
                <button type="submit" class="btn-military">
                    <i class="fa-solid fa-save"></i> Simpan Unit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT UNIT -->
<div id="modalEditUnit" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <button type="button" class="modal-close-btn" onclick="closeModal('modalEditUnit')">&times;</button>
        
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem;">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h3 style="font-size: 1.2rem; color: #fff; margin: 0;">Edit Unit / Subunit</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;" id="editUnitSubtitle">Perbarui detail unit organisasi</p>
            </div>
        </div>

        <form id="formEditUnit" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label" for="edit_name">Nama Unit / Subunit <span style="color: var(--accent-red);">*</span></label>
                <input type="text" id="edit_name" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_code">Kode Singkatan</label>
                <input type="text" id="edit_code" name="code" class="form-control">
                <input type="hidden" id="edit_sort_order" name="sort_order" value="0">
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_parent_id">Unit Induk (Parent)</label>
                <select id="edit_parent_id" name="parent_id" class="form-control">
                    <option value="">-- Tidak Ada (Tingkat Paling Atas) --</option>
                    @foreach($allUnits as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-top: 14px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 0.9rem;">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <span>Status Unit Aktif</span>
                </label>
                <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 0.775rem;">
                    Jika dinonaktifkan, unit ini tidak akan muncul pada pilihan penugasan baru.
                </small>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="closeModal('modalEditUnit')">Batal</button>
                <button type="submit" class="btn-military">
                    <i class="fa-solid fa-save"></i> Perbarui Unit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL TUGASKAN PEJABAT -->
<div id="modalTugaskanPejabat" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content" style="max-width: 560px;">
        <button type="button" class="modal-close-btn" onclick="closeModal('modalTugaskanPejabat')">&times;</button>
        
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, rgba(217, 119, 6, 0.25), rgba(245, 158, 11, 0.1)); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem;">
                <i class="fa-solid fa-user-tag"></i>
            </div>
            <div>
                <h3 style="font-size: 1.2rem; color: #fff; margin: 0;">Tugaskan Pejabat Unit</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;" id="assignModalSubtitle">Pilih unit kerja dan personel penanggung jawab</p>
            </div>
        </div>

        <form action="{{ route('admin.organization.assign') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="assign_unit_id">Pilih Unit / Subbagian <span style="color: var(--accent-red);">*</span></label>
                <select id="assign_unit_id" name="organization_unit_id" class="form-control" required style="font-size: 0.9rem;">
                    <option value="">-- Pilih Unit / Subunit --</option>
                    @foreach(($assignableUnits ?? $allUnits) as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="assign_role">Role Jabatan <span style="color: var(--accent-red);">*</span></label>
                <select id="assign_role" name="role" class="form-control" required style="font-size: 0.9rem;">
                    <optgroup label="Unsur Pimpinan">
                        <option value="kabeng">Kepala Bengpuskomlek (Kepala / Kabeng)</option>
                        <option value="wakabeng">Wakil Kepala Bengpuskomlek (Wakil Kepala / Wakabeng)</option>
                    </optgroup>
                    <optgroup label="Unsur Pembantu Pimpinan">
                        <option value="kabagum">Kabagum (Kepala Bagian Umum)</option>
                        <option value="kabagrendal">Kabagrendal (Kepala Bagian Rendalev)</option>
                        <option value="kabag">Kabag (Kepala Bagian)</option>
                    </optgroup>
                    <optgroup label="Unsur Pelayanan">
                        <option value="pasituud">Pasituud (Perwira Seksi TUUD)</option>
                    </optgroup>
                    <optgroup label="Unsur Pelaksana (Kabeng & Kagud)">
                        <option value="kabengsiskom">Kabeng Siskom</option>
                        <option value="kabengsislek">Kabeng Sislek</option>
                        <option value="kabengjaringan_tik">Kabeng Jaringan & TIK</option>
                        <option value="kabengintegrasi_power">Kabeng Integrasi & Power System</option>
                        <option value="kagud">Kagud (Kepala Gudang)</option>
                    </optgroup>
                    <optgroup label="Kepala Subbengkel (Subbeng)">
                        <option value="kasub">Kasub / Kasubbeng (Kepala Subbengkel)</option>
                    </optgroup>
                    <optgroup label="Perwira Seksi (Pasi) & Lainnya">
                        <option value="pasipam">Pasipam (Perwira Seksi Pengamanan)</option>
                        <option value="pasiops">Pasiops (Perwira Seksi Operasi)</option>
                        <option value="pasipers">Pasipers (Perwira Seksi Personalia)</option>
                        <option value="pasilog">Pasilog (Perwira Seksi Logistik)</option>
                        <option value="pasirendal">Pasirendal (Perwira Seksi Rendal)</option>
                        <option value="kaprim">Kaprim (Kepala Primkopad / Koperasi)</option>
                        <option value="kepala_unit">Kepala Unit / Komandan Satuan</option>
                        <option value="plh">PLH (Pelaksana Harian)</option>
                        <option value="pejabat_lain">Pejabat Lainnya</option>
                    </optgroup>
                </select>
                <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 0.775rem;">
                    Menentukan sebutan kedudukan pada surat dan verifikasi hierarki.
                </small>
            </div>

            <div class="form-group">
                <label class="form-label" for="assign_personel_id">Pilih Personel Pejabat (Perwira ke Atas) <span style="color: var(--accent-red);">*</span></label>
                <select id="assign_personel_id" name="personel_id" class="form-control" required style="font-size: 0.9rem;">
                    <option value="">-- Pilih Perwira Aktif --</option>
                    @foreach($personels as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->nama }} — {{ $p->pangkat_golongan }} ({{ $p->nrp_nip }})
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); display: block; margin-top: 4px; font-size: 0.775rem;">
                    <i class="fa-solid fa-shield"></i> Hanya menampilkan personel militer berpangkat Perwira (Letda s/d Pati).
                </small>
                <small style="color: var(--accent-gold); display: block; margin-top: 6px; font-size: 0.775rem;">
                    <i class="fa-solid fa-circle-info"></i> Jika pejabat pada unit dan jabatan ini sudah ada, pejabat lama akan otomatis dinonaktifkan dan tersimpan ke riwayat.
                </small>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTugaskanPejabat')">Batal</button>
                <button type="submit" class="btn-military">
                    <i class="fa-solid fa-check"></i> Simpan Penugasan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Tabs Styling */
    .tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.9rem;
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tab-btn:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.04);
    }

    .tab-btn.active {
        color: var(--accent-gold);
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    /* Organigram Board & Nodes Styling */
    .orgas-diagram-board {
        user-select: none;
    }

    .orgas-tier {
        position: relative;
    }

    .orgas-tier-label {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #94a3b8;
        padding-left: 20px;
        border-left: 2px dashed rgba(255, 255, 255, 0.15);
        white-space: nowrap;
    }

    .orgas-box-clickable {
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .orgas-box-clickable:hover {
        transform: translateY(-2px);
        filter: brightness(1.15);
        border-color: var(--accent-gold) !important;
    }

    .orgas-card-box {
        background: rgba(15, 23, 42, 0.85);
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 14px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
    }

    .orgas-card-box.orgas-box-green {
        background: linear-gradient(135deg, rgba(6, 78, 59, 0.5) 0%, rgba(6, 95, 70, 0.3) 100%);
        border: 1.5px solid #10b981;
    }

    .orgas-card-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: 0.04em;
        line-height: 1.3;
    }

    .orgas-card-title.text-green {
        color: #34d399;
    }

    .orgas-officer-name {
        color: #f1f5f9;
        font-weight: 700;
        font-size: 0.825rem;
        margin-top: 4px;
        line-height: 1.3;
    }

    .orgas-officer-meta {
        color: var(--accent-gold);
        font-size: 0.725rem;
        margin-top: 2px;
    }

    .orgas-officer-empty {
        color: #94a3b8;
        font-size: 0.725rem;
        font-style: italic;
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .orgas-officer-empty.text-green {
        color: #6ee7b7;
    }

    .orgas-subbeng-header {
        font-size: 0.75rem;
        font-weight: 700;
        color: #cbd5e1;
        margin: 12px 0 6px 4px;
    }

    .orgas-subbeng-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .orgas-subbeng-item {
        background: rgba(16, 185, 129, 0.1);
        border: 1.2px solid rgba(16, 185, 129, 0.35);
        border-radius: 8px;
        padding: 8px 10px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .orgas-subbeng-title {
        font-size: 0.75rem;
        font-weight: 800;
        color: #e2e8f0;
        letter-spacing: 0.03em;
        line-height: 1.25;
    }

    .orgas-subbeng-officer {
        color: #34d399;
        font-weight: 600;
        font-size: 0.7rem;
        margin-top: 3px;
        line-height: 1.2;
    }

    .orgas-subbeng-empty {
        color: #94a3b8;
        font-size: 0.68rem;
        font-style: italic;
        margin-top: 2px;
    }

    /* Role Badges */
    .role-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .role-gold {
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }

    .role-blue {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.35);
    }

    .role-purple {
        background: rgba(168, 85, 247, 0.2);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.35);
    }

    /* Official Pill in Table */
    .official-pill {
        background: rgba(255, 255, 255, 0.035);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 6px;
        transition: all 0.2s ease;
    }

    .official-pill:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.15);
    }

    /* Action Buttons in Table */
    .btn-action-primary {
        background: rgba(5, 150, 105, 0.15);
        border: 1px solid rgba(5, 150, 105, 0.3);
        color: #34d399;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-action-primary:hover {
        background: rgba(5, 150, 105, 0.3);
        color: #fff;
    }

    .btn-action-edit {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action-edit:hover {
        background: rgba(245, 158, 11, 0.15);
        border-color: rgba(245, 158, 11, 0.3);
        color: #fbbf24;
    }

    .icon-deactivate-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 0.8rem;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 4px;
        transition: all 0.15s ease;
    }

    .icon-deactivate-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
    }

    /* Custom Modal Overlay & Content */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(4, 7, 15, 0.82);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeInOverlay 0.2s ease forwards;
    }

    @keyframes fadeInOverlay {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .custom-modal-content {
        background: linear-gradient(145deg, #131b2e 0%, #0d1424 100%);
        border: 1px solid var(--border-color);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.6);
        border-radius: 18px;
        padding: 28px;
        max-width: 520px;
        width: 100%;
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.95);
        animation: scaleUpModal 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes scaleUpModal {
        to { transform: scale(1); }
    }

    .modal-close-btn {
        position: absolute;
        top: 16px;
        right: 18px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.4);
    }
</style>

@push('scripts')
<script>
    // Tab switching logic
    function switchTab(tabId) {
        // Hide all panes
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        // Deactivate all tab buttons
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

        // Show target pane
        const targetPane = document.getElementById('tab-pane-' + tabId);
        if (targetPane) {
            targetPane.style.display = 'block';
        }

        // Activate button
        const targetBtn = document.getElementById('btn-tab-' + tabId);
        if (targetBtn) {
            targetBtn.classList.add('active');
        }
    }

    // Modal helpers
    function openModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.style.display = 'flex';
        }
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.style.display = 'none';
        }
    }

    // Assign Official Modal Helper
    function openAssignModal(unitId, unitName, defaultRole) {
        if (unitId) {
            document.getElementById('assign_unit_id').value = unitId;
            document.getElementById('assignModalSubtitle').innerText = `Menugaskan pejabat untuk: ${unitName}`;
            if (defaultRole) {
                document.getElementById('assign_role').value = defaultRole;
            } else if (unitName) {
                const u = unitName.toUpperCase();
                if (u === 'KEPALA' || u === 'KABENG' || u.includes('KEPALA BENGPUSKOMLEK')) {
                    document.getElementById('assign_role').value = 'kabeng';
                } else if (u.includes('WAKIL') || u.includes('WAKABENG') || u.includes('WAKA')) {
                    document.getElementById('assign_role').value = 'wakabeng';
                } else if (u.includes('KABAGRENDAL') || u.includes('RENDAL')) {
                    document.getElementById('assign_role').value = 'kabagrendal';
                } else if (u.includes('KABAGUM') || u.includes('BAGUM')) {
                    document.getElementById('assign_role').value = 'kabagum';
                } else if (u.includes('TUUD') || u.includes('PASITUUD')) {
                    document.getElementById('assign_role').value = 'pasituud';
                } else if (u.includes('KAGUD') || u.includes('GUDANG')) {
                    document.getElementById('assign_role').value = 'kagud';
                } else if (u.includes('SUBBENG')) {
                    document.getElementById('assign_role').value = 'kasub';
                } else if (u.includes('SISKOM')) {
                    document.getElementById('assign_role').value = 'kabengsiskom';
                } else if (u.includes('SISLEK')) {
                    document.getElementById('assign_role').value = 'kabengsislek';
                } else if (u.includes('JARINGAN') || u.includes('TIK')) {
                    document.getElementById('assign_role').value = 'kabengjaringan_tik';
                } else if (u.includes('INTEGRASI') || u.includes('POWER')) {
                    document.getElementById('assign_role').value = 'kabengintegrasi_power';
                } else if (u.includes('KOPERASI')) {
                    document.getElementById('assign_role').value = 'kaprim';
                } else if (u.includes('SIPAM')) {
                    document.getElementById('assign_role').value = 'pasipam';
                } else if (u.includes('SIOPS')) {
                    document.getElementById('assign_role').value = 'pasiops';
                } else if (u.includes('SIPERS')) {
                    document.getElementById('assign_role').value = 'pasipers';
                } else if (u.includes('SILOG')) {
                    document.getElementById('assign_role').value = 'pasilog';
                } else if (u.includes('BENG')) {
                    document.getElementById('assign_role').value = 'kabag';
                }
            }
        } else {
            document.getElementById('assign_unit_id').value = '';
            document.getElementById('assignModalSubtitle').innerText = 'Pilih unit kerja dan personel penanggung jawab';
        }
        openModal('modalTugaskanPejabat');
    }

    // Edit Unit Modal Helper
    function openEditUnitModal(id, name, code, parentId, level, sortOrder, isActive) {
        document.getElementById('editUnitSubtitle').innerText = `Mengubah unit: ${name}`;
        document.getElementById('formEditUnit').action = `/admin/organization/unit/${id}`;
        
        document.getElementById('edit_name').value = name || '';
        document.getElementById('edit_code').value = code || '';
        document.getElementById('edit_sort_order').value = sortOrder || 0;
        document.getElementById('edit_parent_id').value = parentId || '';
        document.getElementById('edit_is_active').checked = (isActive == 1);
        
        openModal('modalEditUnit');
    }

    // Close modal on click outside content
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal-overlay')) {
            e.target.style.display = 'none';
        }
    });

    // Close modal on ESC key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.custom-modal-overlay').forEach(el => el.style.display = 'none');
        }
    });
</script>
@endpush
@endsection
