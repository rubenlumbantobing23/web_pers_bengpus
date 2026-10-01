<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminTemplateController extends Controller
{
    public function index()
    {
        $templates = [
            'permohonan_perwira' => [
                'name' => 'Surat Permohonan Cuti (Perwira)',
                'description' => 'Template yang diunduh oleh Anggota Perwira saat mengajukan cuti.',
                'filename' => 'template_permohonan_perwira.docx',
                'placeholders' => [
                    '${nama}' => 'Nama lengkap',
                    '${pangkat}' => 'Pangkat / Golongan',
                    '${nrp}' => 'NRP / NIP',
                    '${jabatan}' => 'Jabatan',
                    '${corps}' => 'Corps pemohon',
                    '${corps_pemohon}' => 'Corps pemohon',
                    '${jenis_cuti}' => 'Jenis cuti',
                    '${tgl_mulai}' => 'Tanggal mulai',
                    '${tgl_selesai}' => 'Tanggal selesai',
                    '${total_hari}' => 'Total hari',
                    '${tujuan}' => 'Alamat tujuan',
                    '${kendaraan}' => 'Kendaraan',
                    '${pengikut}' => 'Pengikut',
                    '${nama_kabag}' => 'Nama Kabag',
                    '${pangkat_kabag}' => 'Pangkat Kabag',
                    '${corps_kabag}' => 'Corps Kabag (dari nominatif penandatangan)',
                    '${nrp_kabag}' => 'NRP Kabag',
                    '${nama_kabagum}' => 'Nama Kabagum',
                    '${pangkat_kabagum}' => 'Pangkat Kabagum',
                    '${corps_kabagum}' => 'Corps Kabagum (dari nominatif penandatangan)',
                    '${nrp_kabagum}' => 'NRP Kabagum',
                    '${nama_waka}' => 'Nama Waka',
                    '${pangkat_waka}' => 'Pangkat Waka',
                    '${corps_waka}' => 'Corps Waka (dari nominatif penandatangan)',
                    '${nrp_waka}' => 'NRP Waka',
                    '${nama_kabeng}' => 'Nama Kabeng',
                    '${pangkat_kabeng}' => 'Pangkat Kabeng',
                    '${corps_kabeng}' => 'Corps Kabeng (dari nominatif penandatangan)',
                    '${nrp_kabeng}' => 'NRP Kabeng',
                ]
            ],
            'permohonan_bintara_tamtama' => [
                'name' => 'Surat Permohonan Cuti (Bintara & Tamtama)',
                'description' => 'Template yang diunduh oleh Anggota Bintara & Tamtama saat mengajukan cuti.',
                'filename' => 'template_permohonan_bintara_tamtama.docx',
                'placeholders' => [
                    '${nama}' => 'Nama lengkap',
                    '${pangkat}' => 'Pangkat / Golongan',
                    '${nrp}' => 'NRP / NIP',
                    '${jabatan}' => 'Jabatan',
                    '${corps}' => 'Corps pemohon',
                    '${corps_pemohon}' => 'Corps pemohon',
                    '${jenis_cuti}' => 'Jenis cuti',
                    '${tgl_mulai}' => 'Tanggal mulai',
                    '${tgl_selesai}' => 'Tanggal selesai',
                    '${total_hari}' => 'Total hari',
                    '${tujuan}' => 'Alamat tujuan',
                    '${kendaraan}' => 'Kendaraan',
                    '${pengikut}' => 'Pengikut',
                    '${nama_kabag}' => 'Nama Kabag',
                    '${pangkat_kabag}' => 'Pangkat Kabag',
                    '${corps_kabag}' => 'Corps Kabag (dari nominatif penandatangan)',
                    '${nrp_kabag}' => 'NRP Kabag',
                    '${nama_kabagum}' => 'Nama Kabagum',
                    '${pangkat_kabagum}' => 'Pangkat Kabagum',
                    '${corps_kabagum}' => 'Corps Kabagum (dari nominatif penandatangan)',
                    '${nrp_kabagum}' => 'NRP Kabagum',
                    '${nama_waka}' => 'Nama Waka',
                    '${pangkat_waka}' => 'Pangkat Waka',
                    '${corps_waka}' => 'Corps Waka (dari nominatif penandatangan)',
                    '${nrp_waka}' => 'NRP Waka',
                ]
            ],
            'permohonan_pns' => [
                'name' => 'Surat Permohonan Cuti (PNS)',
                'description' => 'Template yang diunduh oleh Anggota PNS saat mengajukan cuti.',
                'filename' => 'template_permohonan_pns.docx',
                'placeholders' => [
                    '${nama}' => 'Nama lengkap',
                    '${pangkat}' => 'Pangkat / Golongan',
                    '${nrp}' => 'NRP / NIP',
                    '${jabatan}' => 'Jabatan',
                    '${corps}' => 'Corps pemohon',
                    '${corps_pemohon}' => 'Corps pemohon',
                    '${jenis_cuti}' => 'Jenis cuti',
                    '${tgl_mulai}' => 'Tanggal mulai',
                    '${tgl_selesai}' => 'Tanggal selesai',
                    '${total_hari}' => 'Total hari',
                    '${tujuan}' => 'Alamat tujuan',
                    '${kendaraan}' => 'Kendaraan',
                    '${pengikut}' => 'Pengikut',
                    '${nama_kabag}' => 'Nama Kabag',
                    '${pangkat_kabag}' => 'Pangkat Kabag',
                    '${corps_kabag}' => 'Corps Kabag (dari nominatif penandatangan)',
                    '${nrp_kabag}' => 'NRP Kabag',
                    '${nama_kabagum}' => 'Nama Kabagum',
                    '${pangkat_kabagum}' => 'Pangkat Kabagum',
                    '${corps_kabagum}' => 'Corps Kabagum (dari nominatif penandatangan)',
                    '${nrp_kabagum}' => 'NRP Kabagum',
                    '${nama_waka}' => 'Nama Waka',
                    '${pangkat_waka}' => 'Pangkat Waka',
                    '${corps_waka}' => 'Corps Waka (dari nominatif penandatangan)',
                    '${nrp_waka}' => 'NRP Waka',
                ]
            ],
            'surat_cuti_perwira' => [
                'name' => 'Surat Cuti Resmi (Perwira)',
                'description' => 'Template Surat Cuti resmi untuk Perwira.',
                'filename' => 'template_surat_cuti_perwira.docx',
                'placeholders' => [
                    '${nama}' => 'Nama lengkap',
                    '${pangkat}' => 'Pangkat / Golongan',
                    '${nrp}' => 'NRP / NIP',
                    '${jabatan}' => 'Jabatan',
                    '${corps_pemohon}' => 'Corps pemohon',
                    '${jenis_cuti}' => 'Jenis cuti',
                    '${tgl_mulai}' => 'Tanggal mulai',
                    '${tgl_selesai}' => 'Tanggal selesai',
                    '${total_hari}' => 'Total hari',
                    '${tujuan}' => 'Alamat tujuan',
                    '${kendaraan}' => 'Kendaraan',
                    '${pengikut}' => 'Pengikut',
                    '${kodim}' => 'Kodim / Koramil tujuan',
                    '${nama_kabeng}' => 'Nama Kabeng',
                    '${pangkat_kabeng}' => 'Pangkat Kabeng',
                    '${corps_kabeng}' => 'Corps Kabeng (dari nominatif penandatangan)',
                    '${nrp_kabeng}' => 'NRP Kabeng',
                    '${corps}' => 'Corps penandatangan',
                    '${nama_penandatangan}' => 'Nama Penandatangan',
                    '${pangkat_penandatangan}' => 'Pangkat Penandatangan',
                    '${corps_penandatangan}' => 'Corps Penandatangan',
                    '${nrp_penandatangan}' => 'NRP Penandatangan',
                ]
            ],
            'surat_cuti_bintara_tamtama' => [
                'name' => 'Surat Cuti Resmi (Bintara & Tamtama)',
                'description' => 'Template Surat Cuti resmi untuk Bintara & Tamtama.',
                'filename' => 'template_surat_cuti_bintara_tamtama.docx',
                'placeholders' => [
                    '${nama}' => 'Nama lengkap',
                    '${pangkat}' => 'Pangkat / Golongan',
                    '${nrp}' => 'NRP / NIP',
                    '${jabatan}' => 'Jabatan',
                    '${corps_pemohon}' => 'Corps pemohon',
                    '${jenis_cuti}' => 'Jenis cuti',
                    '${tgl_mulai}' => 'Tanggal mulai',
                    '${tgl_selesai}' => 'Tanggal selesai',
                    '${total_hari}' => 'Total hari',
                    '${tujuan}' => 'Alamat tujuan',
                    '${kendaraan}' => 'Kendaraan',
                    '${pengikut}' => 'Pengikut',
                    '${kodim}' => 'Kodim / Koramil tujuan',
                    '${nama_waka}' => 'Nama Waka',
                    '${pangkat_waka}' => 'Pangkat Waka',
                    '${corps_waka}' => 'Corps Waka (dari nominatif penandatangan)',
                    '${nrp_waka}' => 'NRP Waka',
                    '${corps}' => 'Corps penandatangan',
                    '${nama_penandatangan}' => 'Nama Penandatangan',
                    '${pangkat_penandatangan}' => 'Pangkat Penandatangan',
                    '${corps_penandatangan}' => 'Corps Penandatangan',
                    '${nrp_penandatangan}' => 'NRP Penandatangan',
                ]
            ],
            'surat_cuti_pns' => [
                'name' => 'Surat Cuti Resmi (PNS)',
                'description' => 'Template Surat Cuti Dinas yang diterbitkan admin.',
                'filename' => 'template_surat_cuti_pns.docx',
                'placeholders' => [
                    '${nama}' => 'Nama lengkap',
                    '${pangkat}' => 'Pangkat / Golongan',
                    '${nrp}' => 'NRP / NIP',
                    '${jabatan}' => 'Jabatan',
                    '${corps}' => 'Corps pemohon',
                    '${corps_pemohon}' => 'Corps pemohon',
                    '${tujuan}' => 'Alamat tujuan',
                    '${kendaraan}' => 'Kendaraan',
                    '${pengikut}' => 'Pengikut',
                    '${jenis_cuti}' => 'Jenis cuti',
                    '${tgl_mulai}' => 'Tanggal mulai',
                    '${tgl_selesai}' => 'Tanggal selesai',
                    '${total_hari}' => 'Total hari',
                    '${kodim}' => 'Kodim / Koramil tempat melapor',
                    '${tgl_terbit}' => 'Tanggal surat diterbitkan',
                    '${nama_penandatangan}' => 'Nama Penandatangan (Kabeng)',
                    '${pangkat_penandatangan}' => 'Pangkat Penandatangan',
                    '${corps_penandatangan}' => 'Corps Penandatangan',
                    '${nrp_penandatangan}' => 'NRP Penandatangan',
                ]
            ],
            // ─── MODULE PENGAJUAN NIKAH ─────────────────────────────────────
            'surat_izin_nikah' => [
                'name' => 'Surat Izin Nikah',
                'description' => 'Surat Izin Nikah yang diterbitkan Pers setelah semua dokumen diverifikasi.',
                'filename' => 'template_surat_izin_nikah.docx',
                'placeholders' => [
                    '${nama}' => 'Nama anggota', '${pangkat}' => 'Pangkat/Gol', '${nrp}' => 'NRP/NIP',
                    '${jabatan}' => 'Jabatan', '${corps}' => 'Corps', '${satuan}' => 'Satuan',
                    '${anggota_peran}' => 'Peran anggota (Calon Suami/Istri)',
                    '${pasangan_peran}' => 'Peran pasangan', '${pasangan_sebutan}' => 'Sebutan pasangan (huruf kecil)',
                    '${pasangan_nama}' => 'Nama pasangan', '${pasangan_ttl}' => 'TTL pasangan',
                    '${pasangan_agama}' => 'Agama pasangan', '${pasangan_pekerjaan}' => 'Pekerjaan pasangan',
                    '${pasangan_alamat}' => 'Alamat pasangan',
                    '${nikah_tanggal}' => 'Tanggal nikah', '${nikah_hari}' => 'Hari nikah',
                    '${nikah_tempat}' => 'Tempat nikah', '${nikah_alamat}' => 'Alamat nikah',
                    '${tgl_pengajuan}' => 'Tgl pengajuan', '${tgl_terbit}' => 'Tgl surat terbit',
                    '${nama_penandatangan}' => 'Nama Kabeng', '${pangkat_penandatangan}' => 'Pangkat Kabeng',
                    '${nrp_penandatangan}' => 'NRP Kabeng',
                ]
            ],
            'surat_pengantar_na' => [
                'name' => 'Surat Pengantar NA',
                'description' => 'Surat Pengantar Nikah (NA) dari satuan.',
                'filename' => 'template_surat_pengantar_na.docx',
                'placeholders' => [
                    '${nama}' => 'Nama anggota', '${pangkat}' => 'Pangkat/Gol', '${nrp}' => 'NRP',
                    '${pasangan_nama}' => 'Nama pasangan', '${nikah_tanggal}' => 'Tanggal rencana nikah',
                    '${tgl_pengajuan}' => 'Tgl surat', '${nama_penandatangan}' => 'Nama Kabeng',
                ]
            ],
            'surat_pengantar_kesdam' => [
                'name' => 'Surat Pengantar Rikes Kesdam',
                'description' => 'Surat pengantar pemeriksaan kesehatan Kesdam III/Siliwangi.',
                'filename' => 'template_surat_pengantar_kesdam.docx',
                'placeholders' => [
                    '${nama}' => 'Nama anggota', '${pangkat}' => 'Pangkat/Gol', '${nrp}' => 'NRP',
                    '${jabatan}' => 'Jabatan', '${satuan}' => 'Satuan',
                    '${pasangan_nama}' => 'Nama pasangan', '${tgl_pengajuan}' => 'Tgl surat',
                    '${nama_penandatangan}' => 'Nama Kabeng',
                ]
            ],
            'surat_pengantar_bintaldam' => [
                'name' => 'Surat Pengantar Rikes Bintaldam',
                'description' => 'Surat pengantar pemeriksaan mental Bintaldam III/Siliwangi.',
                'filename' => 'template_surat_pengantar_bintaldam.docx',
                'placeholders' => [
                    '${nama}' => 'Nama anggota', '${pangkat}' => 'Pangkat/Gol', '${nrp}' => 'NRP',
                    '${pasangan_nama}' => 'Nama pasangan', '${tgl_pengajuan}' => 'Tgl surat',
                    '${nama_penandatangan}' => 'Nama Kabeng',
                ]
            ],
            'surat_pengantar_litpers' => [
                'name' => 'Surat Pengantar Litpers',
                'description' => 'Surat pengantar penelitian personel calon pasangan ke Kabangpam.',
                'filename' => 'template_surat_pengantar_litpers.docx',
                'placeholders' => [
                    '${nama}' => 'Nama anggota', '${pangkat}' => 'Pangkat/Gol', '${nrp}' => 'NRP',
                    '${pasangan_nama}' => 'Nama pasangan', '${tgl_pengajuan}' => 'Tgl surat',
                    '${nama_penandatangan}' => 'Nama Kabeng',
                ]
            ],
            'surat_skbd' => [
                'name' => 'Surat Permohonan SKBD',
                'description' => 'Surat Permohonan Surat Keterangan Bebas Dinas (SKBD).',
                'filename' => 'template_surat_skbd.docx',
                'placeholders' => [
                    '${nama}' => 'Nama anggota', '${pangkat}' => 'Pangkat/Gol', '${nrp}' => 'NRP',
                    '${jabatan}' => 'Jabatan', '${satuan}' => 'Satuan', '${tgl_pengajuan}' => 'Tgl surat',
                    '${nama_penandatangan}' => 'Nama Kabeng',
                ]
            ],
            'surat_persetujuan_ortua' => [
                'name' => 'Surat Persetujuan Orang Tua/Wali',
                'description' => 'Template surat persetujuan orang tua/wali pasangan.',
                'filename' => 'template_surat_persetujuan_ortua.docx',
                'placeholders' => [
                    '${pasangan_bapak_nama}' => 'Nama bapak pasangan',
                    '${pasangan_nama}' => 'Nama pasangan', '${pasangan_peran}' => 'Peran pasangan',
                    '${nikah_tanggal}' => 'Tanggal nikah', '${tgl_pengajuan}' => 'Tgl surat',
                ]
            ],
            'surat_kesanggupan_pasangan' => [
                'name' => 'Surat Kesanggupan Calon Pasangan',
                'description' => 'Surat kesanggupan dari calon pasangan.',
                'filename' => 'template_surat_kesanggupan_pasangan.docx',
                'placeholders' => [
                    '${pasangan_nama}' => 'Nama pasangan', '${pasangan_ttl}' => 'TTL pasangan',
                    '${pasangan_peran}' => 'Peran pasangan', '${nama}' => 'Nama anggota',
                    '${nikah_tanggal}' => 'Tanggal nikah', '${tgl_pengajuan}' => 'Tgl surat',
                ]
            ],
            'surat_ket_usia' => [
                'name' => 'Surat Keterangan Usia Calon Pasangan',
                'description' => 'Surat keterangan usia calon pasangan. TODO: validasi batas usia sesuai ketentuan Pers.',
                'filename' => 'template_surat_ket_usia.docx',
                'placeholders' => [
                    '${pasangan_nama}' => 'Nama pasangan', '${pasangan_tanggal_lahir}' => 'Tgl lahir pasangan',
                    '${pasangan_ttl}' => 'TTL pasangan', '${tgl_pengajuan}' => 'Tgl surat',
                ]
            ],
        ];

        // Check if templates exist
        foreach ($templates as $key => &$template) {
            $path = storage_path('app/templates/' . $template['filename']);
            $template['exists'] = file_exists($path);
            $template['last_modified'] = $template['exists'] ? filemtime($path) : null;
        }

        return view('admin.templates.index', compact('templates'));
    }

    public function upload(Request $request)
    {
        $validTypes = [
            'permohonan_perwira', 
            'permohonan_bintara_tamtama', 
            'permohonan_pns', 
            'surat_cuti_perwira', 
            'surat_cuti_bintara_tamtama',
            'surat_cuti_pns',
            // Nikah templates
            'surat_izin_nikah',
            'surat_pengantar_na',
            'surat_pengantar_kesdam',
            'surat_pengantar_bintaldam',
            'surat_pengantar_litpers',
            'surat_skbd',
            'surat_persetujuan_ortua',
            'surat_kesanggupan_pasangan',
            'surat_ket_usia',
        ];

        $request->validate([
            'template_type' => 'required|in:' . implode(',', $validTypes),
            'template_file' => 'required|file|mimes:docx|max:5120',
        ], [
            'template_file.mimes' => 'File template harus berformat .docx (Word).',
            'template_file.max' => 'Ukuran file maksimal adalah 5MB.',
        ]);

        $type = $request->template_type;
        $filename = 'template_' . $type . '.docx';

        // Ensure directory exists
        $directory = storage_path('app/templates');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        // Store the uploaded file, overwriting existing
        $file = $request->file('template_file');
        $file->move($directory, $filename);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template ' . $type . ' berhasil diperbarui.');
    }

    public function download($type)
    {
        $validTypes = [
            'permohonan_perwira', 
            'permohonan_bintara_tamtama', 
            'permohonan_pns', 
            'surat_cuti_perwira', 
            'surat_cuti_bintara_tamtama',
            'surat_cuti_pns',
            // Nikah templates
            'surat_izin_nikah',
            'surat_pengantar_na',
            'surat_pengantar_kesdam',
            'surat_pengantar_bintaldam',
            'surat_pengantar_litpers',
            'surat_skbd',
            'surat_persetujuan_ortua',
            'surat_kesanggupan_pasangan',
            'surat_ket_usia',
        ];

        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        $filename = 'template_' . $type . '.docx';
        $path = storage_path('app/templates/' . $filename);

        if (!file_exists($path)) {
            return back()->with('error', 'File template belum tersedia.');
        }

        return response()->download($path, $filename);
    }
}
