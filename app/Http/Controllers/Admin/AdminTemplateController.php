<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\MarriageDocumentType;

class AdminTemplateController extends Controller
{
    private $cutiTemplates = [
        'permohonan_perwira', 
        'permohonan_bintara_tamtama', 
        'permohonan_pns', 
        'surat_cuti_perwira', 
        'surat_cuti_bintara_tamtama',
        'surat_cuti_pns',
    ];

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
        ];

        // Process Cuti templates
        foreach ($templates as $key => &$template) {
            $path = storage_path('app/templates/' . $template['filename']);
            $template['exists'] = file_exists($path);
            $template['last_modified'] = $template['exists'] ? filemtime($path) : null;
        }

        // Dynamically add Marriage Document Types
        $marriageTypes = MarriageDocumentType::whereNotNull('template_path')->get();
        
        $marriagePlaceholders = [
            '${nama_anggota}' => 'Nama anggota', '${pangkat_anggota}' => 'Pangkat/Gol', '${nrp_anggota}' => 'NRP/NIP',
            '${jabatan_anggota}' => 'Jabatan', '${satuan_anggota}' => 'Satuan',
            '${peran_anggota}' => 'Peran anggota (Calon Suami/Istri)',
            '${peran_pasangan}' => 'Peran pasangan', '${sebutan_pasangan}' => 'Sebutan pasangan (huruf kecil)',
            '${nama_pasangan}' => 'Nama pasangan', '${tempat_lahir_pasangan}' => 'Tempat lahir pasangan',
            '${tgl_lahir_pasangan}' => 'Tgl lahir pasangan',
            '${agama_pasangan}' => 'Agama pasangan', '${pekerjaan_pasangan}' => 'Pekerjaan pasangan',
            '${alamat_pasangan}' => 'Alamat pasangan',
            '${tempat_nikah}' => 'Tempat nikah', '${tanggal_rencana_nikah}' => 'Tanggal rencana nikah',
            '${tanggal_surat}' => 'Tgl surat',
            '${nama_pejabat}' => 'Nama Signer Utama', '${pangkat_pejabat}' => 'Pangkat Signer Utama',
            '${corps_pejabat}' => 'Korps Signer Utama (dari nominatif personel)',
            '${nrp_pejabat}' => 'NRP Signer Utama',
            '${nama_pejabat_mengetahui}' => 'Nama Signer Mengetahui',
            '${nama_pejabat_kabag}' => 'Nama Kabag sesuai unit anggota',
        ];

        foreach ($marriageTypes as $mType) {
            $path = storage_path('app/' . $mType->template_path);
            $templates[$mType->code] = [
                'name' => 'Nikah - ' . $mType->name,
                'description' => 'Template untuk modul Pengajuan Nikah: ' . $mType->name,
                'filename' => basename($mType->template_path),
                'placeholders' => $marriagePlaceholders,
                'exists' => file_exists($path),
                'last_modified' => file_exists($path) ? filemtime($path) : null,
            ];
        }

        return view('admin.templates.index', compact('templates'));
    }

    public function upload(Request $request)
    {
        $type = $request->template_type;

        $isCuti = in_array($type, $this->cutiTemplates);
        $marriageType = null;
        
        if (!$isCuti) {
            $marriageType = MarriageDocumentType::where('code', $type)->whereNotNull('template_path')->first();
            if (!$marriageType) {
                return redirect()->back()->with('error', 'Tipe template tidak valid atau tidak didukung.');
            }
        }

        $request->validate([
            'template_type' => 'required|string',
            'template_file' => 'required|file|mimes:docx|max:5120',
        ], [
            'template_file.mimes' => 'File template harus berformat .docx (Word).',
            'template_file.max' => 'Ukuran file maksimal adalah 5MB.',
        ]);

        $file = $request->file('template_file');
        
        // Basic DOCX validation by trying to open it as ZIP
        $zip = new \ZipArchive();
        $res = $zip->open($file->getRealPath());
        if ($res !== true) {
            return redirect()->back()->with('error', 'File yang diupload bukan DOCX yang valid atau rusak.');
        }
        
        // Ensure it contains word/document.xml
        if ($zip->locateName('word/document.xml') === false) {
            $zip->close();
            return redirect()->back()->with('error', 'File yang diupload tidak memiliki struktur OpenXML (word/document.xml).');
        }
        $zip->close();

        if ($isCuti) {
            $filename = 'template_' . $type . '.docx';
            $directory = storage_path('app/templates');
            
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
            
            $file->move($directory, $filename);
        } else {
            // Marriage Type
            $targetPath = storage_path('app/' . $marriageType->template_path);
            $directory = dirname($targetPath);
            
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
            
            // Backup old file if exists
            if (file_exists($targetPath)) {
                $backupPath = $targetPath . '.bak_' . time();
                copy($targetPath, $backupPath);
            }
            
            // Overwrite exactly the existing file
            $file->move($directory, basename($targetPath));
        }

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template ' . ($isCuti ? $type : $marriageType->name) . ' berhasil diperbarui.');
    }

    public function download($type)
    {
        $isCuti = in_array($type, $this->cutiTemplates);
        $marriageType = null;
        
        if (!$isCuti) {
            $marriageType = MarriageDocumentType::where('code', $type)->whereNotNull('template_path')->first();
            if (!$marriageType) {
                abort(404, 'Tipe template tidak valid.');
            }
        }

        if ($isCuti) {
            $filename = 'template_' . $type . '.docx';
            $path = storage_path('app/templates/' . $filename);
        } else {
            $path = storage_path('app/' . $marriageType->template_path);
            $filename = basename($path);
        }

        if (!file_exists($path)) {
            return back()->with('error', 'File template belum tersedia.');
        }

        return response()->download($path, $filename);
    }
}
