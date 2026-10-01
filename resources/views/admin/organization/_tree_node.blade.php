{{-- 
    _tree_node.blade.php
    Recursive partial view untuk rendering tree unit organisasi berdasarkan parent_id.
    
    Variabel:
      $unit   : App\Models\OrganizationUnit (current node)
      $depth  : integer (0 = root, 1 = child, 2 = grandchild, dst)
--}}

@php
    $paddingLeft  = 24 + ($depth * 32);
    $isRoot       = $depth === 0;
    $isLeaf       = $unit->children->isEmpty();

    // Icon & warna berdasarkan nama atau kedalaman
    $isKabeng     = in_array(strtoupper($unit->name), ['KABENG']);
    $isWakabeng   = in_array(strtoupper($unit->name), ['WAKABENG']);
    $isBeng       = str_starts_with(strtoupper($unit->name), 'BENG');
    $isSubbeng    = str_starts_with(strtoupper($unit->name), 'SUBBENG') || strtoupper($unit->name) === 'POWER SYSTEM';
    $isCategoryHeader = in_array(strtoupper($unit->name), ['KELOMPOK PIMPINAN', 'UNSUR PELAYANAN', 'UNSUR PELAKSANA']);
    $isCardHeader = in_array(strtoupper($unit->name), ['BAGUM', 'GUDANG']);

    if ($isRoot) {
        $iconBg     = 'rgba(245, 158, 11, 0.15)';
        $iconBorder = 'rgba(245, 158, 11, 0.3)';
        $iconColor  = '#fbbf24';
        $icon       = 'fa-layer-group';
        $rowBg      = 'rgba(255,255,255,0.038)';
        $borderTop  = 'border-top: 2px solid rgba(245,158,11,0.18);';
    } elseif ($isKabeng) {
        $iconBg     = 'rgba(245, 158, 11, 0.18)';
        $iconBorder = 'rgba(245, 158, 11, 0.35)';
        $iconColor  = '#fbbf24';
        $icon       = 'fa-crown';
        $rowBg      = 'rgba(255,255,255,0.018)';
        $borderTop  = '';
    } elseif ($isWakabeng) {
        $iconBg     = 'rgba(59, 130, 246, 0.18)';
        $iconBorder = 'rgba(59, 130, 246, 0.35)';
        $iconColor  = '#60a5fa';
        $icon       = 'fa-shield-halved';
        $rowBg      = 'rgba(255,255,255,0.018)';
        $borderTop  = '';
    } elseif ($isBeng) {
        $iconBg     = 'rgba(52, 211, 153, 0.15)';
        $iconBorder = 'rgba(52, 211, 153, 0.3)';
        $iconColor  = '#34d399';
        $icon       = 'fa-screwdriver-wrench';
        $rowBg      = 'rgba(255,255,255,0.015)';
        $borderTop  = '';
    } elseif ($isSubbeng) {
        $iconBg     = 'rgba(168, 85, 247, 0.13)';
        $iconBorder = 'rgba(168, 85, 247, 0.25)';
        $iconColor  = '#c084fc';
        $icon       = 'fa-microchip';
        $rowBg      = 'rgba(255,255,255,0.008)';
        $borderTop  = '';
    } else {
        $iconBg     = 'rgba(59, 130, 246, 0.15)';
        $iconBorder = 'rgba(59, 130, 246, 0.3)';
        $iconColor  = '#60a5fa';
        $icon       = 'fa-folder-open';
        $rowBg      = 'rgba(255,255,255,0.015)';
        $borderTop  = '';
    }

    $iconSize = $isRoot ? '32px' : ($isBeng ? '28px' : '24px');
    $fontSize = $isRoot ? '0.975rem' : ($isBeng ? '0.9rem' : '0.85rem');
    $fontWeight = $isRoot ? '700' : ($depth === 1 ? '600' : '500');
@endphp

@if($isCategoryHeader)
<tr style="background: rgba(255,255,255,0.02); border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.05);">
    <td colspan="5" style="text-align: center; padding: 20px 10px; {{ strtoupper($unit->name) === 'KELOMPOK PIMPINAN' ? 'padding-bottom: 30px;' : '' }}">
        <div style="display: inline-flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: {{ strtoupper($unit->name) === 'KELOMPOK PIMPINAN' ? '25px' : '0' }};">
            <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.15rem;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div style="text-align: left;">
                <div style="font-size: 1.15rem; font-weight: 700; color: #fff; letter-spacing: 0.05em; text-transform: uppercase;">
                    {{ $unit->name }}
                </div>
                @if($unit->children->isNotEmpty() && strtoupper($unit->name) !== 'KELOMPOK PIMPINAN')
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                        Membawahi {{ $unit->children->count() }} Unit/Bagian
                    </div>
                @endif
            </div>
        </div>

        @if(strtoupper($unit->name) === 'KELOMPOK PIMPINAN' && $unit->children->isNotEmpty())
            <!-- KABENG dan WAKABENG side-by-side -->
            <div style="display: flex; gap: 20px; justify-content: center; max-width: 900px; margin: 0 auto; text-align: left;">
                @foreach($unit->children as $child)
                    <div style="flex: 1; background: rgba(0,0,0,0.25); border-radius: 10px; padding: 15px 20px; border: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 12px; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: {{ strtoupper($child->name) === 'KABENG' ? 'rgba(245, 158, 11, 0.15)' : 'rgba(59, 130, 246, 0.15)' }}; border: 1px solid {{ strtoupper($child->name) === 'KABENG' ? 'rgba(245, 158, 11, 0.3)' : 'rgba(59, 130, 246, 0.3)' }}; display: flex; align-items: center; justify-content: center; color: {{ strtoupper($child->name) === 'KABENG' ? '#fbbf24' : '#60a5fa' }}; font-size: 1.1rem;">
                                    <i class="fa-solid {{ strtoupper($child->name) === 'KABENG' ? 'fa-crown' : 'fa-shield-halved' }}"></i>
                                </div>
                                <div>
                                    <div style="font-size: 1.1rem; font-weight: 700; color: #fff; letter-spacing: 0.05em; text-transform: uppercase;">
                                        {{ $child->name }}
                                    </div>
                                    <code style="background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px; color: #cbd5e1; font-size: 0.75rem;">
                                        {{ $child->code ?? '-' }}
                                    </code>
                                </div>
                            </div>
                            <button type="button" class="btn-action-edit" onclick="openEditUnitModal('{{ $child->id }}', '{{ e($child->name) }}', '{{ e($child->code) }}', '{{ $child->parent_id }}', '{{ $child->level }}', '{{ $child->sort_order }}', {{ $child->is_active ? 1 : 0 }})" title="Edit Unit" style="background: transparent; border: none; font-size: 0.85rem; padding: 4px;">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </div>

                        <!-- Render assignments for child -->
                        @php $hasActiveChild = false; @endphp
                        @foreach($child->assignments as $assignment)
                            @if($assignment->is_active)
                                @php $hasActiveChild = true; @endphp
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; background: rgba(255,255,255,0.03); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <div style="color: #fbbf24; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 2px;">{{ $assignment->role_label }}</div>
                                            <div style="color: #fff; font-weight: 600; font-size: 1rem;">{{ $assignment->personel->nama }}</div>
                                            <div style="color: var(--text-muted); font-size: 0.8rem;">{{ $assignment->personel->pangkat_golongan }} ({{ $assignment->personel->nrp_nip }})</div>
                                        </div>
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <button type="button" class="btn-action-edit" onclick="openAssignModal('{{ $child->id }}', '{{ e($child->name) }}')" title="Kelola Pejabat" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border-color: rgba(59, 130, 246, 0.3); font-size: 0.75rem; padding: 4px 8px;">
                                            <i class="fa-solid fa-pen-to-square"></i> Kelola
                                        </button>
                                        <form action="{{ route('admin.organization.assignment.deactivate', $assignment->id) }}" method="POST" data-confirm="Penugasan {{ $assignment->personel->nama }} akan dinonaktifkan." data-confirm-title="Nonaktifkan penugasan?" data-confirm-button="Ya, nonaktifkan">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn-action-edit" style="background: rgba(239, 68, 68, 0.1); color: #f87171; border-color: rgba(239, 68, 68, 0.2); font-size: 0.75rem; padding: 4px 8px; width: 100%;" title="Nonaktifkan Penugasan">
                                                <i class="fa-solid fa-xmark"></i> Copot
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(!$hasActiveChild)
                            <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.02); padding: 12px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.1);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text-muted); display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <div style="color: rgba(255,255,255,0.4); font-size: 0.9rem; font-style: italic;">Belum ada pejabat</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-action-primary" onclick="openAssignModal('{{ $child->id }}', '{{ e($child->name) }}')" title="Tugaskan Pejabat" style="font-size: 0.75rem; padding: 4px 10px;">
                                    <i class="fa-solid fa-user-plus"></i> Kelola
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </td>
</tr>
@elseif($isCardHeader)
<tr style="background: rgba(255,255,255,0.02); border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.05);">
    <td colspan="5" style="padding: 24px 30px;">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.5rem;">
                    <i class="fa-solid {{ strtoupper($unit->name) === 'GUDANG' ? 'fa-box-open' : 'fa-building' }}"></i>
                </div>
                <div>
                    <div style="font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: 0.05em; text-transform: uppercase;">
                        {{ $unit->name }}
                    </div>
                    <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 2px;">
                        {{ strtoupper($unit->name) === 'GUDANG' ? 'Gudang' : 'Bagian Umum' }}
                    </div>
                </div>
            </div>
            <div>
                <code style="background: rgba(255,255,255,0.06); padding: 5px 10px; border-radius: 6px; color: #cbd5e1; font-size: 0.85rem;">
                    {{ $unit->code ?? '-' }}
                </code>
            </div>
        </div>

        <div style="background: rgba(0,0,0,0.25); border-radius: 10px; padding: 15px 20px; border: 1px solid rgba(255,255,255,0.05);">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
                Kepala Unit
            </div>
            
            @php $hasActive = false; @endphp
            @foreach($unit->assignments as $assignment)
                @if($assignment->is_active)
                    @php $hasActive = true; @endphp
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; background: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <div style="color: #fbbf24; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; margin-bottom: 2px;">{{ $assignment->role_label }}</div>
                                <div style="color: #fff; font-weight: 600; font-size: 1.05rem;">{{ $assignment->personel->nama }}</div>
                                <div style="color: var(--text-muted); font-size: 0.85rem;">{{ $assignment->personel->pangkat_golongan }} ({{ $assignment->personel->nrp_nip }})</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button type="button" class="btn-action-edit" onclick="openAssignModal('{{ $unit->id }}', '{{ e($unit->name) }}')" title="Kelola Pejabat" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border-color: rgba(59, 130, 246, 0.3);">
                                <i class="fa-solid fa-pen-to-square"></i> Kelola
                            </button>
                            <form action="{{ route('admin.organization.assignment.deactivate', $assignment->id) }}" method="POST" data-confirm="Penugasan {{ $assignment->personel->nama }} akan dinonaktifkan." data-confirm-title="Nonaktifkan penugasan?" data-confirm-button="Ya, nonaktifkan">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn-action-edit" style="background: rgba(239, 68, 68, 0.1); color: #f87171; border-color: rgba(239, 68, 68, 0.2);" title="Nonaktifkan Penugasan">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach

            @if(!$hasActive)
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.02); padding: 12px 15px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.1);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <div style="color: var(--text-muted); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; margin-bottom: 2px;">{{ strtoupper($unit->name) === 'GUDANG' ? 'KAGUD' : 'KABAGUM' }}</div>
                            <div style="color: rgba(255,255,255,0.4); font-size: 0.95rem; font-style: italic;">Belum ada pejabat</div>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn-action-primary" onclick="openAssignModal('{{ $unit->id }}', '{{ e($unit->name) }}')" title="Tugaskan Pejabat">
                            <i class="fa-solid fa-user-plus"></i> Kelola
                        </button>
                    </div>
                </div>
            @endif
        </div>

        @if($unit->children->isNotEmpty())
        <div style="margin-top: 20px; font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
            UNIT DI BAWAH {{ $unit->name }}
        </div>
        @endif
    </td>
</tr>
@else
<tr style="background: {{ $rowBg }}; {{ $borderTop }}">
    <td style="padding-left: {{ $paddingLeft }}px; padding-top: 10px; padding-bottom: 10px;">
        <div style="display: flex; align-items: center; gap: 9px;">
            @if($depth > 0)
                <span style="color: var(--text-muted); font-family: monospace; font-size: 1rem; opacity: 0.5; flex-shrink: 0;">
                    @if($unit->children->isEmpty()) └── @else ├── @endif
                </span>
            @endif
            <div style="width: {{ $iconSize }}; height: {{ $iconSize }}; border-radius: {{ $isRoot ? '8px' : '6px' }}; background: {{ $iconBg }}; border: 1px solid {{ $iconBorder }}; display: flex; align-items: center; justify-content: center; color: {{ $iconColor }}; font-size: {{ $isRoot ? '0.9rem' : '0.75rem' }}; flex-shrink: 0;">
                <i class="fa-solid {{ $icon }}"></i>
            </div>
            <div>
                <div style="font-size: {{ $fontSize }}; font-weight: {{ $fontWeight }}; color: #fff; letter-spacing: 0.01em;">
                    {{ $unit->name }}
                </div>
                @if($isRoot && $unit->children->isNotEmpty())
                    <div style="font-size: 0.71rem; color: var(--text-muted); margin-top: 1px;">
                        {{ $unit->children->count() }} subunit
                    </div>
                @endif
            </div>
        </div>
    </td>
    <td>
        <code style="background: rgba(255,255,255,0.06); padding: 3px 7px; border-radius: 5px; color: #cbd5e1; font-size: 0.78rem;">
            {{ $unit->code ?? '-' }}
        </code>
    </td>
    <td>
        @if($unit->is_active)
            <span class="badge" style="background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3);">
                <i class="fa-solid fa-check"></i> Aktif
            </span>
        @else
            <span class="badge" style="background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3);">
                Nonaktif
            </span>
        @endif
    </td>
    <td>
        @php $hasActive = false; @endphp
        @foreach($unit->assignments as $assignment)
            @if($assignment->is_active)
                @php $hasActive = true; @endphp
                <div class="official-pill">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <span class="role-badge role-gold">{{ $assignment->role_label }}</span>
                        <form action="{{ route('admin.organization.assignment.deactivate', $assignment->id) }}" method="POST" data-confirm="Penugasan {{ $assignment->personel->nama }} akan dinonaktifkan." data-confirm-title="Nonaktifkan penugasan?" data-confirm-button="Ya, nonaktifkan">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="icon-deactivate-btn" title="Nonaktifkan Penugasan">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>
                    </div>
                    <div style="color: #fff; font-weight: 600; font-size: 0.875rem; margin-top: 4px;">{{ $assignment->personel->nama }}</div>
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
    </td>
    <td style="text-align: center;">
        <div style="display: inline-flex; gap: 6px;">
            <button type="button" class="btn-action-primary" onclick="openAssignModal('{{ $unit->id }}', '{{ e($unit->name) }}')" title="Tugaskan Pejabat">
                <i class="fa-solid fa-user-plus"></i> Pejabat
            </button>
            <button type="button" class="btn-action-edit" onclick="openEditUnitModal('{{ $unit->id }}', '{{ e($unit->name) }}', '{{ e($unit->code) }}', '{{ $unit->parent_id }}', '{{ $unit->level }}', '{{ $unit->sort_order }}', {{ $unit->is_active ? 1 : 0 }})" title="Edit Unit">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>
    </td>
</tr>
@endif

{{-- Recursive: render children --}}
@if(strtoupper($unit->name) !== 'KELOMPOK PIMPINAN')
    @foreach($unit->children as $child)
        @include('admin.organization._tree_node', ['unit' => $child, 'depth' => $depth + 1])
    @endforeach
@endif
