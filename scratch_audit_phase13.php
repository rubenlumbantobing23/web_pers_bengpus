<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MarriageApplication;
use App\Models\Personel;
use App\Models\MarriagePartner;
use App\Services\MarriageLetterGenerator;

$output = [
    'hash_before' => [],
    'hash_after' => [],
    'results' => []
];

// 1. Get Template Paths
$docTypes = DB::table('marriage_document_types')
    ->select('id', 'code', 'name', 'template_path')
    ->whereNotNull('template_path')
    ->get();

// 2. Hash Before
foreach ($docTypes as $doc) {
    $fullPath = storage_path('app/' . $doc->template_path);
    if (file_exists($fullPath)) {
        $output['hash_before'][$doc->code] = md5_file($fullPath);
    }
}

// 3. Define Scenarios
$scenarios = [
    'A' => ['peran_anggota' => 'suami', 'jenis_kelamin_anggota' => 'Pria', 'peran_pasangan' => 'istri', 'status_pernikahan' => 'Gadis', 'expected' => 'seorang gadis'],
    'B' => ['peran_anggota' => 'istri', 'jenis_kelamin_anggota' => 'Wanita', 'peran_pasangan' => 'suami', 'status_pernikahan' => 'Jejaka', 'expected' => 'seorang jejaka'],
    'C' => ['peran_anggota' => 'suami', 'jenis_kelamin_anggota' => 'Pria', 'peran_pasangan' => 'istri', 'status_pernikahan' => 'Janda', 'expected' => 'seorang janda'],
    'D' => ['peran_anggota' => 'istri', 'jenis_kelamin_anggota' => 'Wanita', 'peran_pasangan' => 'suami', 'status_pernikahan' => 'Duda', 'expected' => 'seorang duda'],
];

// 4. Generate & Test
$generator = new MarriageLetterGenerator();

foreach ($scenarios as $scName => $scData) {
    // Create mock objects
    $personel = new Personel([
        'nama' => "DUMMY_ANGGOTA_$scName",
        'pangkat_golongan' => 'Sersan Dummy',
        'nrp_nip' => '123456789',
        'corps' => 'CHK',
        'jabatan' => 'Bintara Dummy',
        'satuan_bagian' => 'Bengpuskomlekad',
        'jenis_kelamin' => $scData['jenis_kelamin_anggota'],
        'agama' => 'Islam',
        'suku' => 'Jawa',
        'tempat_lahir' => 'Bandung',
        'tgl_lahir' => now()->subYears(25),
        'no_hp' => '0812345678'
    ]);
    // Simulate DB saving for related fields handling (since models might check if they exist or not, but we just need attributes)
    
    $partner = new MarriagePartner([
        'peran' => $scData['peran_pasangan'],
        'status_pernikahan' => $scData['status_pernikahan'],
        'nama' => "DUMMY_PASANGAN_$scName",
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => now()->subYears(22),
        'pekerjaan' => 'Swasta',
        'agama' => 'Islam',
        'suku' => 'Sunda',
        'alamat' => 'Jl. Dummy Pasangan No 1',
        'bapak_nama' => 'Bapak Dummy',
        'bapak_agama' => 'Islam',
        'bapak_pekerjaan' => 'PNS',
        'bapak_alamat' => 'Jl. Dummy Bapak',
        'ibu_nama' => 'Ibu Dummy',
        'ibu_agama' => 'Islam',
        'ibu_pekerjaan' => 'IRT',
        'ibu_alamat' => 'Jl. Dummy Ibu'
    ]);
    
    $application = new MarriageApplication([
        'jenis_kelamin_anggota' => $scData['jenis_kelamin_anggota'],
        'peran_anggota' => $scData['peran_anggota'],
        'tanggal_rencana_nikah' => now()->addMonth(),
        'tempat_nikah' => 'KUA Dummy',
        'alamat_nikah' => 'Jl. Nikah',
        'kelurahan_nikah' => 'Kel Nikah',
        'kecamatan_nikah' => 'Kec Nikah',
        'provinsi_nikah' => 'Prov Nikah',
        'alamat_domisili' => 'Jl. Domisili',
        'kelurahan_domisili' => 'Kel Domisili',
        'kecamatan_domisili' => 'Kec Domisili',
        'kabupaten_domisili' => 'Kab Domisili',
        'provinsi_domisili' => 'Prov Domisili',
        'kua_tujuan' => 'KUA Tujuan Dummy'
    ]);
    $application->id = 999999; // Mock ID
    $application->setRelation('personel', $personel);
    $application->setRelation('partner', $partner);
    
    foreach ($docTypes as $doc) {
        $templatePath = storage_path('app/' . $doc->template_path);
        $outputName = "TEST_{$scName}_{$doc->code}";
        
        $res = [
            'scenario' => $scName,
            'template' => $doc->code,
            'generated' => false,
            'valid_zip' => false,
            'xml_valid' => false,
            'placeholder_clean' => false,
            'expected_mapping' => false,
            'unresolved' => [],
            'error' => null
        ];
        
        try {
            $generatedPath = $generator->generate($application, $templatePath, $outputName);
            $fullGenPath = storage_path("app/private/" . $generatedPath);
            
            if (file_exists($fullGenPath)) {
                $res['generated'] = true;
                
                $zip = new ZipArchive();
                if ($zip->open($fullGenPath) === true) {
                    $res['valid_zip'] = true;
                    $xml = $zip->getFromName('word/document.xml');
                    
                    if ($xml !== false) {
                        // Suppress warnings for invalid XML during load
                        libxml_use_internal_errors(true);
                        $dom = new DOMDocument();
                        if ($dom->loadXML($xml)) {
                            $res['xml_valid'] = true;
                            // Strip tags to get plain text
                            $text = strip_tags($xml);
                            
                            // Check residue
                            preg_match_all('/\$\{[^}]+\}/', $text, $matches);
                            if (empty($matches[0])) {
                                $res['placeholder_clean'] = true;
                            } else {
                                $res['unresolved'] = array_unique($matches[0]);
                            }
                            
                            // Expected Mapping
                            if (stripos($text, $scData['expected']) !== false) {
                                $res['expected_mapping'] = true;
                            }
                        }
                        libxml_clear_errors();
                    }
                    $zip->close();
                }
            }
        } catch (\Exception $e) {
            $res['error'] = $e->getMessage();
        }
        
        $output['results'][] = $res;
    }
}

// 5. Clean up tests (Remove folder 999999)
Storage::disk('private')->deleteDirectory('marriage_letters/999999');

// 6. Hash After
foreach ($docTypes as $doc) {
    $fullPath = storage_path('app/' . $doc->template_path);
    if (file_exists($fullPath)) {
        $output['hash_after'][$doc->code] = md5_file($fullPath);
    }
}

file_put_contents('scratch_audit_phase13.json', json_encode($output, JSON_PRETTY_PRINT));
echo "DONE\n";
