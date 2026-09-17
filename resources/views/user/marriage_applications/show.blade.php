@extends('layouts.user')

@section('page-title', 'Detail Pengajuan Nikah')

@section('user-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Top Info Bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                <a href="{{ route('user.pengajuan_nikah.index') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
            <h2 style="font-size: 1.4rem; color: #fff; font-weight: 700;">
                Pengajuan Nikah #{{ $application->id }}
                — {{ $application->peran_anggota }}
            </h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                Diajukan: {{ $application->tanggal_pengajuan->format('d M Y') }}
                | Rencana Nikah: {{ $application->tanggal_rencana_nikah->format('d M Y') }}
            </p>
        </div>
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
            @php
                $statusConfig = [
                    'DRAFT'           => ['class' => 'badge-secondary', 'icon' => 'fa-pen'],
                    'DIAJUKAN'        => ['class' => 'badge-info',      'icon' => 'fa-paper-plane'],
                    'PERLU_PERBAIKAN' => ['class' => 'badge-warning',   'icon' => 'fa-triangle-exclamation'],
                    'DIVERIFIKASI'    => ['class' => 'badge-primary',   'icon' => 'fa-magnifying-glass-check'],
                    'DISETUJUI'       => ['class' => 'badge-success',   'icon' => 'fa-check-double'],
                    'SELESAI'         => ['class' => 'badge-success',   'icon' => 'fa-circle-check'],
                    'DITOLAK'         => ['class' => 'badge-danger',    'icon' => 'fa-ban'],
                ];
                $cfg = $statusConfig[$application->status] ?? ['class' => 'badge-secondary', 'icon' => 'fa-circle'];
            @endphp
            <span class="badge {{ $cfg['class'] }}" style="font-size: 1rem; padding: 10px 18px;">
                <i class="fa-solid {{ $cfg['icon'] }}"></i>
                {{ str_replace('_', ' ', $application->status) }}
            </span>
            @if($application->status === 'DRAFT')
                <form action="{{ route('user.pengajuan_nikah.submit', $application->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-military" style="font-size: 0.85rem; padding: 8px 14px;" onclick="return confirm('Yakin ingin mengajukan ke Admin sekarang?')">
                        <i class="fa-solid fa-paper-plane"></i> Submit ke Admin
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Alert: PERLU_PERBAIKAN --}}
    @if($application->status === 'PERLU_PERBAIKAN')
    <div style="background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.3); padding: 14px 18px; border-radius: 10px; color: #fde68a;">
        <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Pengajuan Membutuhkan Perbaikan</div>
        @if($application->catatan_admin)
            <div style="font-size: 0.9rem;">Catatan Admin: {{ $application->catatan_admin }}</div>
        @endif
        <div style="font-size: 0.85rem; margin-top: 8px; color: #fbbf24;">Silakan unggah ulang dokumen yang ditolak (lihat di panel dokumen). Setelah upload, pengajuan akan otomatis dikembalikan ke status DIAJUKAN.</div>
    </div>
    @endif

    {{-- Alert: DITOLAK --}}
    @if($application->status === 'DITOLAK')
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); padding: 14px 18px; border-radius: 10px; color: #fca5a5;">
        <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-ban"></i> Pengajuan Ditolak</div>
        @if($application->catatan_admin)
            <div style="font-size: 0.9rem;">Alasan: {{ $application->catatan_admin }}</div>
        @endif
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px; align-items: start;">

        {{-- LEFT COLUMN --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- Data Anggota --}}
            <div class="glass-card">
                <h3 style="font-size: 1rem; color: var(--accent-gold); font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px;">
                    <i class="fa-solid fa-user-shield"></i> Data Anggota ({{ $application->peran_anggota }})
                </h3>
                @php $p = $application->personel; @endphp
                <table style="width: 100%; font-size: 0.9rem; color: #e2e8f0; border-collapse: collapse;">
                    @foreach(['Nama' => $p->nama, 'NRP/NIP' => $p->nrp_nip, 'Pangkat' => $p->pangkat_golongan, 'Jabatan' => $p->jabatan, 'Satuan' => $p->satuan_bagian] as $lbl => $val)
                    <tr>
                        <td style="padding: 6px 0; width: 38%; color: var(--text-muted);">{{ $lbl }}</td>
                        <td style="padding: 6px 0; font-weight: 600;">: {{ $val ?? '-' }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>

            {{-- Data Pernikahan --}}
            <div class="glass-card">
                <h3 style="font-size: 1rem; color: var(--accent-gold); font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px;">
                    <i class="fa-solid fa-calendar-day"></i> Rencana Pernikahan
                </h3>
                <table style="width: 100%; font-size: 0.9rem; color: #e2e8f0; border-collapse: collapse;">
                    <tr><td style="padding: 6px 0; width: 38%; color: var(--text-muted);">Tanggal</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->tanggal_rencana_nikah->format('d M Y') }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Tempat</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->tempat_nikah }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Alamat</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->alamat_nikah }}, Kel. {{ $application->kelurahan_nikah }}, Kec. {{ $application->kecamatan_nikah }}, Kab/Kota {{ $application->kabupaten_nikah }}, Prov. {{ $application->provinsi_nikah }}</td></tr>
                </table>
            </div>

            {{-- Data Pasangan --}}
            @if($application->partner)
            <div class="glass-card">
                <h3 style="font-size: 1rem; color: var(--accent-gold); font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px;">
                    <i class="fa-solid fa-user-heart"></i> Data {{ $application->partner->peran }}
                </h3>
                <table style="width: 100%; font-size: 0.9rem; color: #e2e8f0; border-collapse: collapse;">
                    <tr><td style="padding: 6px 0; width: 40%; color: var(--text-muted);">Nama</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->partner->nama }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Tempat, Tgl Lahir</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->partner->tempat_lahir }}, {{ $application->partner->tanggal_lahir->format('d M Y') }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Pekerjaan</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->partner->pekerjaan }} ({{ $application->partner->status_pekerjaan }})</td></tr>
                    @if($application->partner->status_pekerjaan === 'ASN')
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Instansi / Jabatan</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->partner->instansi }} — {{ $application->partner->jabatan }}</td></tr>
                    @endif
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Agama / Suku</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->partner->agama }} / {{ $application->partner->suku }}</td></tr>
                    <tr><td style="padding: 6px 0; color: var(--text-muted);">Alamat</td><td style="padding: 6px 0; font-weight: 600;">: {{ $application->partner->alamat }}, Kel. {{ $application->partner->kelurahan }}, Kec. {{ $application->partner->kecamatan }}, {{ $application->partner->kabupaten }}</td></tr>
                </table>

                <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.06);">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">Orang Tua / Wali</div>
                    <table style="width: 100%; font-size: 0.88rem; color: #e2e8f0; border-collapse: collapse;">
                        <tr><td style="padding: 4px 0; width: 40%; color: var(--text-muted);">Bapak/Wali</td><td style="padding: 4px 0;">: {{ $application->partner->bapak_nama }} | {{ $application->partner->bapak_agama }} | {{ $application->partner->bapak_pekerjaan }}</td></tr>
                        <tr><td style="padding: 4px 0; color: var(--text-muted);">Ibu</td><td style="padding: 4px 0;">: {{ $application->partner->ibu_nama }} | {{ $application->partner->ibu_agama }} | {{ $application->partner->ibu_pekerjaan }}</td></tr>
                    </table>
                </div>
            </div>
            @else
            <div class="glass-card" style="text-align: center; padding: 30px; color: var(--text-muted);">
                <i class="fa-solid fa-user-slash" style="font-size: 2rem; margin-bottom: 12px; opacity: 0.4;"></i>
                <div>Data pasangan belum diisi.</div>
            </div>
            @endif

            {{-- Riwayat Status --}}
            <div class="glass-card">
                <h3 style="font-size: 1rem; color: var(--accent-gold); font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Status
                </h3>
                @if($application->statusHistories->isEmpty())
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Belum ada riwayat status.</p>
                @else
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($application->statusHistories->sortByDesc('created_at') as $history)
                    <div style="padding: 10px; border-radius: 6px; background: rgba(255,255,255,0.02); border-left: 3px solid
                        @switch($history->status)
                            @case('DISETUJUI') @case('SELESAI') #10b981; @break
                            @case('DITOLAK') @case('PERLU_PERBAIKAN') #f87171; @break
                            @case('DIAJUKAN') #60a5fa; @break
                            @case('DIVERIFIKASI') #a78bfa; @break
                            @default #64748b
                        @endswitch
                    ">
                        <div style="font-weight: 700; color: #fff; font-size: 0.9rem;">{{ str_replace('_', ' ', $history->status) }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                            {{ $history->created_at->format('d M Y, H:i') }} — {{ $history->changer->name ?? 'Sistem' }}
                        </div>
                        @if($history->catatan)
                        <div style="font-size: 0.85rem; color: #cbd5e1; margin-top: 6px; padding: 6px 8px; background: rgba(0,0,0,0.2); border-radius: 4px;">{{ $history->catatan }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Surat Pengantar dari Personalia --}}
            <div class="glass-card">
                <h3 style="font-size: 1rem; color: var(--accent-gold); font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px;">
                    <i class="fa-solid fa-file-signature"></i> Surat Pengantar dari Personalia
                </h3>
                @php
                    $suratPengantar = [
                        'PENGANTAR_NA' => 'Surat Pengantar NA',
                        'PENGANTAR_KESDAM' => 'Surat Pengantar Kesdam',
                        'PENGANTAR_BINTALDAM' => 'Surat Pengantar Bintaldam',
                        'PENGANTAR_LITPERS' => 'Surat Pengantar Litpers',
                        'PENGANTAR_SKBD' => 'Surat Pengantar SKBD'
                    ];
                @endphp
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($suratPengantar as $code => $title)
                    @php 
                        $letter = $application->letters->where('jenis_surat', $code)->first(); 
                    @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: rgba(255,255,255,0.03); border-radius: 6px; border: 1px solid rgba(255,255,255,0.08);">
                        <div>
                            <div style="font-weight: 600; color: #e2e8f0; font-size: 0.9rem;">{{ $title }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                Status: @if($letter) <span class="badge badge-success">Tersedia</span> @else <span style="color: #94a3b8;">Belum Dibuat</span> @endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <form action="{{ route('user.pengajuan_nikah.generate_letter', $application->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type_code" value="{{ $code }}">
                                <button type="submit" class="btn-military" style="font-size: 0.75rem; padding: 6px 10px; background: rgba(100,116,139,0.3); border-color: rgba(100,116,139,0.6);" onclick="return confirm('Generate surat ini sekarang?')">
                                    <i class="fa-solid fa-rotate"></i> Generate
                                </button>
                            </form>
                            @if($letter)
                            <a href="{{ route('user.pengajuan_nikah.download_letter', [$application->id, $letter->id]) }}" class="btn-military" style="font-size: 0.75rem; padding: 6px 10px;">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        {{-- RIGHT COLUMN: Documents --}}
        <div class="glass-card" style="position: sticky; top: 90px;">
            <h3 style="font-size: 1rem; color: var(--accent-gold); font-weight: 700; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 14px;">
                <i class="fa-solid fa-folder-open"></i> Dokumen Persyaratan
            </h3>

            @if(in_array($application->status, ['DRAFT', 'DIAJUKAN', 'PERLU_PERBAIKAN']))
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 14px; padding: 8px; background: rgba(255,255,255,0.03); border-radius: 6px;">
                <i class="fa-solid fa-circle-info"></i> Format: PDF, JPG, PNG. Maks. 5 MB per file.
            </div>
            @endif

            <div style="font-size: 0.75rem; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">
                <i class="fa-solid fa-user"></i> DOKUMEN ANGGOTA ({{ count($requiredAnggota) }})
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
                @foreach($requiredAnggota as $req)
                    @php $doc = $application->documents->where('jenis_dokumen', $req)->first(); @endphp
                    @include('user.marriage_applications._document_item', ['req' => $req, 'doc' => $doc, 'pihak' => 'Anggota'])
                @endforeach
            </div>

            <div style="font-size: 0.75rem; font-weight: 700; color: #f472b6; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">
                <i class="fa-solid fa-user-dress"></i> DOKUMEN {{ strtoupper($application->partner->peran ?? 'PASANGAN') }} ({{ count($requiredPasangan) }})
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($requiredPasangan as $req)
                    @php $doc = $application->documents->where('jenis_dokumen', $req)->first(); @endphp
                    @include('user.marriage_applications._document_item', ['req' => $req, 'doc' => $doc, 'pihak' => 'Pasangan'])
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
