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
                'description' => 'Template Surat Cuti resmi untuk PNS.',
                'filename' => 'template_surat_cuti_pns.docx',
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
            'surat_cuti_pns'
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
            'surat_cuti_pns'
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
