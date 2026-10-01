<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarriageDocumentType;

class MarriageDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            // A. SURAT DARI SATUAN
            ['code' => 'SURAT_PERMOHONAN_IZIN_NIKAH', 'name' => 'Surat Permohonan Izin Nikah', 'category' => 'SURAT_SATUAN', 'owner_type' => 'ANGGOTA', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat pengajuan menikah.docx'],
            ['code' => 'PENGANTAR_NA', 'name' => 'Surat Pengantar Nikah (NA)', 'category' => 'SURAT_SATUAN', 'owner_type' => 'SATUAN', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat pengantar nikah (NA).docx'],
            ['code' => 'PENGANTAR_KESDAM', 'name' => 'Surat Permohonan Pemeriksaan Badan ke Kesdam III/Siliwangi', 'category' => 'SURAT_SATUAN', 'owner_type' => 'SATUAN', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat pengantar kesdam.docx'],
            ['code' => 'PENGANTAR_BINTALDAM', 'name' => 'Surat Permohonan Petunjuk/Pendapat ke Bintaldam III/Siliwangi', 'category' => 'SURAT_SATUAN', 'owner_type' => 'SATUAN', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat pengantar bintal.docx'],
            ['code' => 'PENGANTAR_LITPERS', 'name' => 'Surat Permohonan Penelitian Personel (Litpers)', 'category' => 'SURAT_SATUAN', 'owner_type' => 'SATUAN', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat Pengantar Litpers.docx'],
            ['code' => 'PENGANTAR_SKBD', 'name' => 'Surat Permohonan Surat Keterangan Bersih dari Penelitian Khusus', 'category' => 'SURAT_SATUAN', 'owner_type' => 'SATUAN', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat pengantar bersih diri.docx'],
            ['code' => 'SURAT_KETERANGAN_BELUM_NIKAH', 'name' => 'Surat Keterangan Belum Nikah', 'category' => 'SURAT_SATUAN', 'owner_type' => 'SATUAN', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat keterangan belum menikah.docx'],


            // B. TEMPLATE CALON/PASANGAN
            ['code' => 'PERSETUJUAN_ORANG_TUA_WALI', 'name' => 'Surat Pernyataan Persetujuan Orang Tua/Wali diketahui Lurah/Desa', 'category' => 'TEMPLATE_CALON', 'owner_type' => 'ORANG_TUA_PASANGAN', 'source_type' => 'ORANG_TUA'],
            ['code' => 'KESANGGUPAN_CALON_PASANGAN', 'name' => 'Surat Pernyataan Kesanggupan Calon Suami/Istri diketahui Lurah/Desa', 'category' => 'TEMPLATE_CALON', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],
            ['code' => 'KETERANGAN_ORANG_TUA_WALI', 'name' => 'Surat Keterangan Orang Tua/Wali', 'category' => 'TEMPLATE_CALON', 'owner_type' => 'ORANG_TUA_PASANGAN', 'source_type' => 'ORANG_TUA'],
            ['code' => 'KETERANGAN_USIA_CALON_PASANGAN', 'name' => 'Surat Keterangan Calon Suami/Istri Telah Berusia 17+', 'category' => 'TEMPLATE_CALON', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],

            // C. HASIL INSTANSI LUAR
            ['code' => 'NA_ANGGOTA', 'name' => 'NA/Pengantar Nikah Anggota dari KUA/Desa', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'ANGGOTA', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'NA_DARI_DESA', 'name' => 'NA/Pengantar Nikah Calon Pasangan dari Desa/Kelurahan', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'PASANGAN', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'HASIL_RIKES_KESDAM', 'name' => 'Hasil Pemeriksaan Kesehatan Kesdam III/Siliwangi', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'ANGGOTA', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'HASIL_PEMERIKSAAN_BINTALDAM', 'name' => 'Hasil Pemeriksaan/Petunjuk/Pendapat Bintaldam III/Siliwangi', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'ANGGOTA', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'HASIL_LITPERS', 'name' => 'Sertifikat/Hasil Lolos Litpers', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'ANGGOTA', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'SKBD_KORAMIL', 'name' => 'SKBD/Hasil Penelitian dari Koramil', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'ANGGOTA', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'SKBD_KODIM', 'name' => 'SKBD/Hasil Penelitian dari Kodim', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'ANGGOTA', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'SKCK_ORANG_TUA', 'name' => 'SKCK Orang Tua Anggota', 'category' => 'HASIL_INSTANSI', 'owner_type' => 'ORANG_TUA_ANGGOTA', 'source_type' => 'INSTANSI_LUAR'],

            // D. DOKUMEN PASANGAN
            ['code' => 'KK_CALON_PASANGAN', 'name' => 'Fotokopi KK Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],
            ['code' => 'AKTA_KELAHIRAN_CALON_PASANGAN', 'name' => 'Fotokopi Akta Kelahiran Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],
            ['code' => 'KTP_CALON_PASANGAN', 'name' => 'Fotokopi KTP Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],
            ['code' => 'IJAZAH_TERAKHIR_CALON_PASANGAN', 'name' => 'Fotokopi Ijazah Terakhir Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],
            ['code' => 'PAS_FOTO_BERDAMPINGAN', 'name' => 'Pas Foto Biru 9x6 pakaian Persit tanpa lencana, berdampingan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],
            ['code' => 'PAS_FOTO_4X6_CALON_ISTRI_ORANG_TUA', 'name' => 'Pas Foto 4x6 Calon Istri + Kedua Orang Tua', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],
            ['code' => 'LITPERS_CALON_PASANGAN', 'name' => 'Litpers Calon Suami/Istri', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'LITPERS_ORANG_TUA_CALON_PASANGAN', 'name' => 'Litpers Orang Tua Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'ORANG_TUA_PASANGAN', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'SKCK_CALON_PASANGAN', 'name' => 'SKCK Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN', 'name' => 'Surat Keterangan Dinas Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'INSTANSI_LUAR'],
            ['code' => 'DOKUMEN_LAIN_CALON_PASANGAN', 'name' => 'Dokumen Lain Calon Pasangan', 'category' => 'DOKUMEN_PASANGAN', 'owner_type' => 'PASANGAN', 'source_type' => 'PASANGAN'],

            // E. SURAT FINAL
            ['code' => 'SURAT_IZIN_NIKAH_FINAL', 'name' => 'Surat Izin Nikah', 'category' => 'SURAT_FINAL', 'owner_type' => 'ANGGOTA', 'source_type' => 'SYSTEM', 'template_path' => 'templates/marriage/Surat izin nikah.docx'],
        ];

        $sortOrder = 10;
        foreach ($documents as $doc) {
            MarriageDocumentType::updateOrCreate(
                ['code' => $doc['code']],
                array_merge($doc, [
                    'is_required' => false,
                    'is_active' => true,
                    'sort_order' => $sortOrder
                ])
            );
            $sortOrder += 10;
        }
    }
}
