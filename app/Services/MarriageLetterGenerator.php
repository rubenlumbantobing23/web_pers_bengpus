<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\MarriageApplication;
use Illuminate\Support\Facades\Storage;

class MarriageLetterGenerator
{
    public function generate(MarriageApplication $application, string $templatePath, string $outputName): string
    {
        $templateProcessor = new class($templatePath) extends TemplateProcessor {
            public function __construct($documentTemplate)
            {
                parent::__construct($documentTemplate);
                $this->tempDocumentMainPart = str_replace('<w:t>', '<w:t xml:space="preserve">', $this->tempDocumentMainPart);
                foreach ($this->tempDocumentHeaders as $index => $xml) {
                    $this->tempDocumentHeaders[$index] = str_replace('<w:t>', '<w:t xml:space="preserve">', $xml);
                }
                foreach ($this->tempDocumentFooters as $index => $xml) {
                    $this->tempDocumentFooters[$index] = str_replace('<w:t>', '<w:t xml:space="preserve">', $xml);
                }
            }
        };

        $personel = $application->personel;
        $partner  = $application->partner;

        // 1. Data Anggota
        $templateProcessor->setValue('nama_anggota',          $personel->nama ?? '');
        $templateProcessor->setValue('pangkat_anggota',       trim($personel->pangkat_golongan ?? ''));
        $templateProcessor->setValue('nrp_anggota',           trim($personel->nrp_nip ?? ''));
        $templateProcessor->setValue('corps_anggota',         trim($personel->corps ?? ''));
        $templateProcessor->setValue('jabatan_anggota',       str_replace(["\r", "\n"], ' ', $personel->jabatan ?? ''));
        $templateProcessor->setValue('satuan_anggota',        $personel->satuan_bagian ?? 'Bengpuskomlekad');
        $templateProcessor->setValue('jenis_kelamin_anggota', $personel->jenis_kelamin ?? '');
        $templateProcessor->setValue('agama_anggota',         $personel->agama ?? '-');
        $templateProcessor->setValue('suku_anggota',          $personel->suku ?? '-');
        $templateProcessor->setValue('tempat_lahir_anggota',  $personel->tempat_lahir ?? '-');
        $tglLahirAnggota = $personel->tgl_lahir ? $personel->tgl_lahir->locale('id')->isoFormat('D MMMM Y') : '-';
        $templateProcessor->setValue('tgl_lahir_anggota',     $tglLahirAnggota);
        $templateProcessor->setValue('no_hp_anggota',         $personel->no_hp ?? '-');

        // 2. Data Pasangan
        if ($partner) {
            $templateProcessor->setValue('nama_pasangan',          $partner->nama ?? '');
            $templateProcessor->setValue('pangkat_pasangan',       $partner->jabatan ?? ''); // Using jabatan/instansi if ASN, else blank
            $templateProcessor->setValue('nrp_pasangan',           $partner->instansi ?? '');
            $templateProcessor->setValue('tempat_lahir_pasangan',  $partner->tempat_lahir ?? '');
            $tglLahirPasangan = $partner->tanggal_lahir ? $partner->tanggal_lahir->locale('id')->isoFormat('D MMMM Y') : '-';
            $templateProcessor->setValue('tgl_lahir_pasangan',     $tglLahirPasangan);
            $templateProcessor->setValue('agama_pasangan',         $partner->agama ?? '');
            $templateProcessor->setValue('suku_pasangan',          $partner->suku ?? '');
            $templateProcessor->setValue('pekerjaan_pasangan',     $partner->pekerjaan ?? '');
            $templateProcessor->setValue('alamat_pasangan',        $partner->alamat ?? '');
        } else {
            $templateProcessor->setValue('nama_pasangan',          '');
            $templateProcessor->setValue('pangkat_pasangan',       '');
            $templateProcessor->setValue('nrp_pasangan',           '');
            $templateProcessor->setValue('tempat_lahir_pasangan',  '');
            $templateProcessor->setValue('tgl_lahir_pasangan',     '');
            $templateProcessor->setValue('agama_pasangan',         '');
            $templateProcessor->setValue('suku_pasangan',          '');
            $templateProcessor->setValue('pekerjaan_pasangan',     '');
            $templateProcessor->setValue('alamat_pasangan',        '');
        }

        // 3. Data Domisili
        $templateProcessor->setValue('alamat_anggota',   $application->alamat_domisili ?? '');
        $templateProcessor->setValue('desa_kelurahan',   $application->kelurahan_domisili ?? '');
        $templateProcessor->setValue('kecamatan',        $application->kecamatan_domisili ?? '');
        $templateProcessor->setValue('kabupaten_kota',   $application->kabupaten_domisili ?? '');
        $templateProcessor->setValue('provinsi',         $application->provinsi_domisili ?? '');
        $templateProcessor->setValue('kua_tujuan',       $application->kua_tujuan ?? '');

        // 4. Data Surat
        $templateProcessor->setValue('nomor_surat',   ''); // Can be filled later if needed
        $templateProcessor->setValue('tanggal_surat', now()->locale('id')->isoFormat('D MMMM Y'));
        $templateProcessor->setValue('perihal',       '');
        $templateProcessor->setValue('tujuan_surat',  '');
        $templateProcessor->setValue('tempat_tujuan', '');

        // 5. Data Pejabat via OrganizationStructureService
        try {
            $structureService = app(\App\Services\OrganizationStructureService::class);
            
            $isFinalLetter = stripos($outputName, 'SURAT_IZIN_NIKAH_FINAL') !== false;
            
            if ($isFinalLetter) {
                $signer = $structureService->getSignerPersonel('kabeng');
                $jabatanPejabat = 'Kepala Bengkel Pusat Komunikasi dan Elektronika TNI AD';
            } else {
                $signer = $structureService->getSignerPersonel('pasipers');
                $jabatanPejabat = "a.n. Kepala Bengkel Pusat Komunikasi dan Elektronika TNI AD\nKabagum\nu.b.\nPasipers";
            }

            if ($signer) {
                $templateProcessor->setValue('nama_pejabat',    $signer->nama);
                $templateProcessor->setValue('pangkat_pejabat', trim($signer->pangkat_golongan));
                $templateProcessor->setValue('jabatan_pejabat', $jabatanPejabat);
            } else {
                $templateProcessor->setValue('nama_pejabat',    '');
                $templateProcessor->setValue('pangkat_pejabat', '');
                $templateProcessor->setValue('jabatan_pejabat', '');
            }
        } catch (\Exception $e) {
            $templateProcessor->setValue('nama_pejabat',    '');
            $templateProcessor->setValue('pangkat_pejabat', '');
            $templateProcessor->setValue('jabatan_pejabat', '');
        }

        // Save to private storage
        $safeOutputName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $outputName);
        $fileName       = time() . '_' . $safeOutputName . '.docx';
        $tempPath       = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $templateProcessor->saveAs($tempPath);

        $finalPath = 'marriage_letters/' . $application->id . '/' . $fileName;
        Storage::disk('private')->put($finalPath, file_get_contents($tempPath));
        @unlink($tempPath);

        return $finalPath;
    }
}
