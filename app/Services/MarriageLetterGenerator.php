<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\MarriageApplication;
use Illuminate\Support\Facades\Storage;

class MarriageLetterGenerator
{
    public function generate(MarriageApplication $application, string $templatePath, string $outputName): string
    {
        $templateProcessor = new TemplateProcessor($templatePath);

        $escapeValue = static function ($value) {
            if ($value === null) return '';
            $str = (string) $value;
            // Escape special XML characters to prevent DOCX truncation
            $str = htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            // Convert newlines to Word break
            $str = str_replace("\n", '<w:br/>', $str);
            return $str;
        };

        $personel = $application->personel;
        $partner  = $application->partner;

        // Istilah pasangan ditentukan otomatis dari peran anggota dan status pernikahan pasangan.
        $statusPernikahan = strtolower($partner->status_pernikahan ?? '');
        $peranAnggota = strtolower($application->peran_anggota ?? '');
        
        $sebutanPasangan = '';
        if ($peranAnggota === 'suami' && $statusPernikahan === 'gadis') {
            $sebutanPasangan = 'seorang gadis';
        } elseif ($peranAnggota === 'suami' && $statusPernikahan === 'janda') {
            $sebutanPasangan = 'seorang janda';
        } elseif ($peranAnggota === 'istri' && $statusPernikahan === 'jejaka') {
            $sebutanPasangan = 'seorang jejaka';
        } elseif ($peranAnggota === 'istri' && $statusPernikahan === 'duda') {
            $sebutanPasangan = 'seorang duda';
        }
        $templateProcessor->setValue('sebutan_pasangan', $escapeValue($sebutanPasangan));
        // 1. Data Anggota
        $templateProcessor->setValue('nama_anggota', $escapeValue($personel->nama ?? ''));
        $templateProcessor->setValue('pangkat_anggota', $escapeValue(trim($personel->pangkat_golongan ?? '')));
        $templateProcessor->setValue('nrp_anggota', $escapeValue(trim($personel->nrp_nip ?? '')));
        $templateProcessor->setValue('corps_anggota', $escapeValue(trim($personel->corps ?? '')));
        $templateProcessor->setValue('jabatan_anggota', $escapeValue($personel->jabatan ?? ''));
        $templateProcessor->setValue('satuan_anggota', $escapeValue($personel->satuan_bagian ?? 'Bengpuskomlekad'));
        $templateProcessor->setValue('jenis_kelamin_anggota', $escapeValue($personel->jenis_kelamin ?? ''));
        $templateProcessor->setValue('agama_anggota', $escapeValue($personel->agama ?? '-'));
        $templateProcessor->setValue('suku_anggota', $escapeValue($personel->suku ?? '-'));
        $templateProcessor->setValue('tempat_lahir_anggota', $escapeValue($personel->tempat_lahir ?? '-'));
        $tglLahirAnggota = $personel->tgl_lahir ? $personel->tgl_lahir->locale('id')->isoFormat('D MMMM Y') : '-';
        $templateProcessor->setValue('tgl_lahir_anggota', $escapeValue($tglLahirAnggota));
        $templateProcessor->setValue('no_hp_anggota', $escapeValue($personel->no_hp ?? '-'));

        // 2. Data Pasangan
        if ($partner) {
            $templateProcessor->setValue('nama_pasangan', $escapeValue($partner->nama ?? ''));
            $templateProcessor->setValue('pangkat_pasangan', $escapeValue($partner->jabatan ?? '')); // Using jabatan/instansi if ASN, else blank
            $templateProcessor->setValue('nrp_pasangan', $escapeValue($partner->instansi ?? ''));
            $templateProcessor->setValue('tempat_lahir_pasangan', $escapeValue($partner->tempat_lahir ?? ''));
            $tglLahirPasangan = $partner->tanggal_lahir ? $partner->tanggal_lahir->locale('id')->isoFormat('D MMMM Y') : '-';
            $templateProcessor->setValue('tgl_lahir_pasangan', $escapeValue($tglLahirPasangan));
            $templateProcessor->setValue('agama_pasangan', $escapeValue($partner->agama ?? ''));
            $templateProcessor->setValue('suku_pasangan', $escapeValue($partner->suku ?? ''));
            $templateProcessor->setValue('pekerjaan_pasangan', $escapeValue($partner->pekerjaan ?? ''));
            $templateProcessor->setValue('alamat_pasangan', $escapeValue($partner->alamat ?? ''));
        } else {
            $templateProcessor->setValue('nama_pasangan', $escapeValue(''));
            $templateProcessor->setValue('pangkat_pasangan', $escapeValue(''));
            $templateProcessor->setValue('nrp_pasangan', $escapeValue(''));
            $templateProcessor->setValue('tempat_lahir_pasangan', $escapeValue(''));
            $templateProcessor->setValue('tgl_lahir_pasangan', $escapeValue(''));
            $templateProcessor->setValue('agama_pasangan', $escapeValue(''));
            $templateProcessor->setValue('suku_pasangan', $escapeValue(''));
            $templateProcessor->setValue('pekerjaan_pasangan', $escapeValue(''));
            $templateProcessor->setValue('alamat_pasangan', $escapeValue(''));
        }

        // 3. Data Domisili
        $templateProcessor->setValue('alamat_anggota', $escapeValue($application->alamat_domisili ?? ''));
        $templateProcessor->setValue('desa_kelurahan', $escapeValue($application->kelurahan_domisili ?? ''));
        $templateProcessor->setValue('kecamatan', $escapeValue($application->kecamatan_domisili ?? ''));
        $templateProcessor->setValue('kabupaten_kota', $escapeValue($application->kabupaten_domisili ?? ''));
        $templateProcessor->setValue('provinsi', $escapeValue($application->provinsi_domisili ?? ''));
        $templateProcessor->setValue('kua_tujuan', $escapeValue($application->kua_tujuan ?? ''));

        // 4. Data Surat
        $templateProcessor->setValue('nomor_surat', $escapeValue('')); // Can be filled later if needed
        $templateProcessor->setValue('tanggal_surat', $escapeValue(now()->locale('id')->isoFormat('D MMMM Y')));
        $templateProcessor->setValue('perihal', $escapeValue(''));
        $templateProcessor->setValue('tujuan_surat', $escapeValue(''));
        $templateProcessor->setValue('tempat_tujuan', $escapeValue(''));

        // 5. Data Pejabat via OrganizationStructureService
        $signer = null;
        $signerMengetahui = null;
        $signerKabag = null;
        $jabatanPejabat = '';
        $corpsPejabat = '';
        try {
            $structureService = app(\App\Services\OrganizationStructureService::class);
            $signerMengetahui = $structureService->getSupervisorForPersonel($personel);
            $signerKabag = $structureService->getKabagForPersonel($personel);
            
            $isFinalLetter = stripos($outputName, 'SURAT_IZIN_NIKAH_FINAL') !== false;
            
            if ($isFinalLetter) {
                $signer = $structureService->getSignerPersonel('kabeng');
                $jabatanPejabat = 'Kepala Bengkel Pusat Komunikasi dan Elektronika TNI AD';
            } else {
                $signer = $structureService->getSignerPersonel('pasipers');
                $jabatanPejabat = "a.n. Kepala Bengkel Pusat Komunikasi dan Elektronika TNI AD\nKabagum\nu.b.\nPasipers";
            }

            if ($signer) {
                $corpsPejabat = $structureService->resolveCorpsForPersonel($signer);
                $templateProcessor->setValue('nama_pejabat', $escapeValue($signer->nama));
                $templateProcessor->setValue('pangkat_pejabat', $escapeValue(trim($signer->pangkat_golongan)));
                $templateProcessor->setValue('jabatan_pejabat', $escapeValue($jabatanPejabat));
            } else {
                $templateProcessor->setValue('nama_pejabat', $escapeValue(''));
                $templateProcessor->setValue('pangkat_pejabat', $escapeValue(''));
                $templateProcessor->setValue('jabatan_pejabat', $escapeValue(''));
            }
        } catch (\Exception $e) {
            $templateProcessor->setValue('nama_pejabat', $escapeValue(''));
            $templateProcessor->setValue('pangkat_pejabat', $escapeValue(''));
            $templateProcessor->setValue('jabatan_pejabat', $escapeValue(''));
        }

        // Shared placeholder map used by all marriage letter templates.
        $formatDate = static fn ($date) => $date ? $date->locale('id')->isoFormat('D MMMM Y') : '';
        $rolePasangan = $partner->peran ?? (strtolower($application->peran_anggota ?? '') === 'suami' ? 'istri' : 'suami');
        $placeholderValues = [
            'peran_anggota' => ucfirst($application->peran_anggota ?? ''),
            'peran_pasangan' => ucfirst($rolePasangan),
            'peran_pasangan_display' => ucfirst($rolePasangan),
            'tempat_tgl_lahir_anggota' => trim(($personel->tempat_lahir ?? '') . ', ' . $tglLahirAnggota, ', '),
            'tempat_tgl_lahir_pasangan' => trim(($partner->tempat_lahir ?? '') . ', ' . ($tglLahirPasangan ?? ''), ', '),
            'kelurahan_domisili' => $application->kelurahan_domisili ?? '',
            'kecamatan_domisili' => $application->kecamatan_domisili ?? '',
            'kabupaten_domisili' => $application->kabupaten_domisili ?? '',
            'provinsi_domisili' => $application->provinsi_domisili ?? '',
            'tanggal_rencana_nikah' => $formatDate($application->tanggal_rencana_nikah),
            'tempat_nikah' => $application->tempat_nikah ?? '',
            'alamat_nikah' => $application->alamat_nikah ?? '',
            'kelurahan_nikah' => $application->kelurahan_nikah ?? '',
            'kecamatan_nikah' => $application->kecamatan_nikah ?? '',
            'kabupaten_nikah' => $application->kabupaten_nikah ?? '',
            'provinsi_nikah' => $application->provinsi_nikah ?? '',
            'nama_kua' => $application->kua_tujuan ?? '',
            'tanggal_surat' => now()->locale('id')->isoFormat('D MMMM Y'),
            'bulan_surat' => now()->locale('id')->isoFormat('MMMM'),
            'tahun_surat' => now()->format('Y'),
            'nomor_surat' => '',
            'tempat_surat' => 'Bandung',
            'bapak_anggota_nama' => $application->bapak_anggota_nama ?: '-',
            'bapak_anggota_agama' => $application->bapak_anggota_agama ?: '-',
            'bapak_anggota_pekerjaan' => $application->bapak_anggota_pekerjaan ?: '-',
            'bapak_anggota_alamat' => $application->bapak_anggota_alamat ?: '-',
            'ibu_anggota_nama' => $application->ibu_anggota_nama ?: '-',
            'ibu_anggota_agama' => $application->ibu_anggota_agama ?: '-',
            'ibu_anggota_pekerjaan' => $application->ibu_anggota_pekerjaan ?: '-',
            'ibu_anggota_alamat' => $application->ibu_anggota_alamat ?: '-',
            'bapak_pasangan_nama' => $partner->bapak_nama ?? '-',
            'bapak_pasangan_agama' => $partner->bapak_agama ?? '-',
            'bapak_pasangan_pekerjaan' => $partner->bapak_pekerjaan ?? '-',
            'bapak_pasangan_alamat' => $partner->bapak_alamat ?? '-',
            'ibu_pasangan_nama' => $partner->ibu_nama ?? '-',
            'ibu_pasangan_agama' => $partner->ibu_agama ?? '-',
            'ibu_pasangan_pekerjaan' => $partner->ibu_pekerjaan ?? '-',
            'ibu_pasangan_alamat' => $partner->ibu_alamat ?? '-',
            'nama_pejabat' => $signer->nama ?? '',
            'pangkat_pejabat' => trim($signer->pangkat_golongan ?? ''),
            'corps_pejabat' => $corpsPejabat,
            'nrp_pejabat' => trim($signer->nrp_nip ?? ''),
            'jabatan_pejabat' => $jabatanPejabat ?? '',
            'nama_pejabat_mengetahui' => $signerMengetahui->nama ?? '-',
            'pangkat_pejabat_mengetahui' => trim($signerMengetahui->pangkat_golongan ?? '-'),
            'nrp_pejabat_mengetahui' => trim($signerMengetahui->nrp_nip ?? '-'),
            'nama_pejabat_kabag' => $signerKabag->nama ?? '-',
        ];
        foreach ($placeholderValues as $key => $value) {
            $templateProcessor->setValue($key, $escapeValue($value));
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
