<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personel;
use App\Models\User;
use App\Models\LeaveEntitlement;
use App\Models\OrganizationUnit;
use App\Exports\PersonelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class AdminPersonelController extends Controller
{
    // ════════════════════════════════════════
    //  INDEX — Daftar Nominatif Personel
    // ════════════════════════════════════════
    public function index(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 25;

        $ranks = [
            'Jenderal', 'Letjen', 'Mayjen', 'Brigjen',
            'Kolonel', 'Letkol', 'Mayor',
            'Kapten', 'Lettu', 'Letda',
            'Peltu', 'Pelda', 'Serma', 'Serka', 'Sertu', 'Serda',
            'Kopka', 'Koptu', 'Kopda', 'Praka', 'Pratu', 'Prada',
            'IV/e', 'IV/d', 'IV/c', 'IV/b', 'IV/a',
            'III/d', 'III/c', 'III/b', 'III/a',
            'II/d', 'II/c', 'II/b', 'II/a',
            'I/d', 'I/c', 'I/b', 'I/a'
        ];
        $ranksStr = implode("','", $ranks);

        $query = Personel::with('user')
            ->orderByRaw("FIELD(pangkat_golongan, '$ranksStr') = 0")
            ->orderByRaw("FIELD(pangkat_golongan, '$ranksStr')")
            ->orderBy('nama');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_personel', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status === 'active');
        }

        $personels    = $query->paginate($perPage)->withQueryString();
        $totalMiliter = Personel::militer()->count();
        $totalPns     = Personel::pns()->count();
        $totalAll     = $totalMiliter + $totalPns;
        $lastUpdated  = Personel::latest('updated_at')->value('updated_at');

        return view('admin.personel.index', compact(
            'personels', 'totalMiliter', 'totalPns', 'totalAll', 'lastUpdated', 'perPage'
        ));
    }

    // ════════════════════════════════════════
    //  CREATE & STORE
    // ════════════════════════════════════════
    // IDs of units that are jabatan/positions, not actual sub-units where personel work.
    // KASUBBENG units (12-31) are work units → they remain available in dropdown.
    // 2=KABAGUM, 7=PASIRENDAL-head, 11=KABENG SISKOM, 15=KABENG SISLEK,
    // 20=KABENG JARINGAN DAN TIK, 23=KABENG INTEGRASI DAN POWER SYSTEM,
    // 25=KAGUD, 32=KEPALA, 33=WAKIL KEPALA, 34=PASITUUD
    const JABATAN_UNIT_IDS = [2, 7, 11, 15, 20, 23, 25, 32, 33, 34];

    public function create()
    {
        $officials = \App\Models\OrganizationOfficialAssignment::where('is_active', true)
            ->with(['personel', 'unit'])
            ->get()
            ->sortBy(function($o) { return $o->unit ? $o->unit->sort_order : 999; });
            
        return view('admin.personel.create', compact('officials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_personel'   => 'required|in:militer,pns',
            'nrp_nip'          => 'required|string|max:50|unique:personels,nrp_nip',
            'nama'             => 'required|string|max:255',
            'kategori_personel'=> 'required|string|in:Perwira Menengah,Perwira Pertama,Bintara,Tamtama,PNS',
            'pangkat_golongan' => 'required|string|max:100',
            'jabatan'          => 'required|string|max:150',
            'satuan_bagian'    => 'required|string|max:150',
            'organization_unit_id' => 'nullable|exists:organization_units,id',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|unique:users,email|unique:personels,email',
            'password'         => 'nullable|string|min:6',
            'tgl_lahir'        => 'nullable|date',
            'status_pernikahan'=> 'nullable|string|in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati',
        ]);

        $user = null;
        if ($request->boolean('create_user') && $request->filled('email')) {
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password ?: 'password'),
                'role'     => 'user',
            ]);
            LeaveEntitlement::create([
                'user_id'    => $user->id,
                'year'       => date('Y'),
                'total_days' => 12,
            ]);
        }

        $personel = Personel::create(array_merge(
            ['user_id' => $user ? $user->id : null, 'status_aktif' => true],
            $this->mapRequestToFields($request)
        ));

        \App\Helpers\ActivityLogger::log('Tambah Personel', "Menambahkan data personel baru: {$personel->nama} ({$personel->nrp_nip})");

        return redirect()->route('admin.personel.index')
            ->with('success', 'Data personel baru berhasil ditambahkan.');
    }

    // ════════════════════════════════════════
    //  SHOW — Detail Personel
    // ════════════════════════════════════════
    public function show($id)
    {
        $personel = Personel::with(['user.leaveRequests.leaveType', 'user.marriageRequests'])
            ->findOrFail($id);

        return view('admin.personel.show', compact('personel'));
    }

    // ════════════════════════════════════════
    //  EDIT & UPDATE
    // ════════════════════════════════════════
    public function edit($id)
    {
        $personel = Personel::with('user')->findOrFail($id);
        $officials = \App\Models\OrganizationOfficialAssignment::where('is_active', true)
            ->with(['personel', 'unit'])
            ->get()
            ->sortBy(function($o) { return $o->unit ? $o->unit->sort_order : 999; });
            
        return view('admin.personel.edit', compact('personel', 'officials'));
    }

    public function update(Request $request, $id)
    {
        $personel = Personel::findOrFail($id);

        $request->validate([
            'jenis_personel'   => 'required|in:militer,pns',
            'nrp_nip'          => 'required|string|max:50|unique:personels,nrp_nip,' . $id,
            'nama'             => 'required|string|max:255',
            'kategori_personel'=> 'required|string|in:Perwira Menengah,Perwira Pertama,Bintara,Tamtama,PNS',
            'pangkat_golongan' => 'required|string|max:100',
            'jabatan'          => 'required|string|max:150',
            'satuan_bagian'    => 'required|string|max:150',
            'organization_unit_id' => 'nullable|exists:organization_units,id',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|unique:personels,email,' . $id,
            'status_aktif'     => 'required|boolean',
            'tgl_lahir'        => 'nullable|date',
            'status_pernikahan'=> 'nullable|string|in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati',
        ]);

        $personel->update(array_merge(
            ['status_aktif' => $request->boolean('status_aktif')],
            $this->mapRequestToFields($request)
        ));

        if ($personel->user) {
            $personel->user->update(['name' => $request->nama]);
        }

        \App\Helpers\ActivityLogger::log('Edit Personel', "Memperbarui data personel: {$personel->nama} ({$personel->nrp_nip})");

        return redirect()->route('admin.personel.index')
            ->with('success', 'Data personel berhasil diperbarui.');
    }

    // ════════════════════════════════════════
    //  DESTROY — Hapus Personel
    // ════════════════════════════════════════
    public function destroy($id)
    {
        $personel = Personel::findOrFail($id);
        $nama     = $personel->nama;
        $nrp      = $personel->nrp_nip;
        $personel->delete();

        \App\Helpers\ActivityLogger::log('Hapus Personel', "Menghapus data personel: {$nama} ({$nrp})");

        return redirect()->route('admin.personel.index')
            ->with('success', "Data personel {$nama} berhasil dihapus.");
    }

    // ════════════════════════════════════════
    //  EXPORT — Download Excel
    // ════════════════════════════════════════
    public function export(Request $request)
    {
        $jenis   = $request->input('jenis', 'semua');
        $filters = $request->only(['search']);

        $filename = match ($jenis) {
            'militer' => 'Nominatif_Militer_' . date('Ymd') . '.xlsx',
            'pns'     => 'Nominatif_PNS_' . date('Ymd') . '.xlsx',
            default   => 'Nominatif_Personel_' . date('Ymd') . '.xlsx',
        };

        return Excel::download(new PersonelExport($jenis, $filters), $filename);
    }

    // ════════════════════════════════════════
    //  IMPORT — Step 1: Form Upload
    // ════════════════════════════════════════
    public function importForm()
    {
        return view('admin.personel.import');
    }

    // ════════════════════════════════════════
    //  IMPORT — Step 2: Preview sebelum simpan
    // ════════════════════════════════════════
    public function importPreview(Request $request)
    {
        $request->validate([
            'file'  => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'jenis' => 'required|in:militer,pns',
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes'    => 'Format file harus CSV, XLS, atau XLSX.',
            'file.max'      => 'Ukuran file maksimal 10MB.',
            'jenis.required'=> 'Pilih jenis personel (Militer / PNS).',
        ]);

        $file    = $request->file('file');
        $ext     = strtolower($file->getClientOriginalExtension());
        $jenis   = $request->jenis;
        $rawRows = [];

        if (in_array($ext, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet   = $spreadsheet->getActiveSheet();
                $rawRows     = $worksheet->toArray(null, true, true, false);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
            }
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            $bom    = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $rawRows[] = $row;
            }
            fclose($handle);
        }

        \Log::info('RAW EXCEL ROWS:', array_slice($rawRows, 0, 10));

        // Cari baris header (yang ada keyword)
        $headerIdx = null;
        $keywords  = ['nama', 'nrp', 'nip', 'pangkat', 'golongan', 'jabatan'];
        foreach ($rawRows as $i => $row) {
            $rowLower = array_map('strtolower', array_map('trim', $row));
            $matches  = 0;
            foreach ($keywords as $kw) {
                foreach ($rowLower as $cell) {
                    if (str_contains($cell, $kw)) { $matches++; break; }
                }
            }
            if ($matches >= 2) { $headerIdx = $i; break; }
        }

        if ($headerIdx === null) {
            return back()->with('error', 'Gagal menemukan baris judul kolom (Header) di file Excel. Pastikan file Anda memiliki kolom seperti NAMA dan NRP/NIP.');
        }

        // Cari max col
        $maxCols = 0;
        foreach ($rawRows as $row) {
            if (count($row) > $maxCols) $maxCols = count($row);
        }

        // Cari baris penomoran (angka 1, 2, 3...)
        $numIdx = null;
        for ($r = $headerIdx + 1; $r <= $headerIdx + 5; $r++) {
            if (!isset($rawRows[$r])) continue;
            $c0 = trim($rawRows[$r][0] ?? '');
            $c1 = trim($rawRows[$r][1] ?? '');
            $c2 = trim($rawRows[$r][2] ?? '');
            $c3 = trim($rawRows[$r][3] ?? '');
            // Jika ada kombinasi 1 dan 2 berdekatan
            if (($c0 === '1' && $c1 === '2') || ($c1 === '1' && $c2 === '2') || ($c2 === '1' && $c3 === '2')) {
                $numIdx = $r;
                break;
            }
        }

        $endMerge = $numIdx !== null ? $numIdx - 1 : $headerIdx + 1;
        $startMerge = max(0, $headerIdx - 1);
        
        $combinedHeaders = [];
        for ($c = 0; $c < $maxCols; $c++) {
            $colText = [];
            for ($r = $startMerge; $r <= $endMerge; $r++) {
                if (isset($rawRows[$r][$c]) && trim($rawRows[$r][$c]) !== '') {
                    $colText[] = trim($rawRows[$r][$c]);
                }
            }
            $combinedHeaders[$c] = strtolower(implode(' ', $colText));
        }

        $colIdx = [];
        foreach ($combinedHeaders as $idx => $col) {
            if (empty($col)) continue;
            if (str_contains($col, 'nama') && !isset($colIdx['nama'])) $colIdx['nama'] = $idx;
            elseif ((str_contains($col, 'nrp') || str_contains($col, 'nip')) && !isset($colIdx['nrp_nip'])) $colIdx['nrp_nip'] = $idx;
            elseif ((str_contains($col, 'pangkat') || str_contains($col, 'gol')) && !str_contains($col, 'tmt') && !isset($colIdx['pangkat'])) $colIdx['pangkat'] = $idx;
            elseif (str_contains($col, 'tmt pangkat') || str_contains($col, 'tmt gol')) $colIdx['tmt_pangkat'] = $idx;
            elseif ((str_contains($col, 'corps') || str_contains($col, 'korps')) && !isset($colIdx['corps'])) $colIdx['corps'] = $idx;
            elseif (str_contains($col, 'jabatan') && !str_contains($col, 'tmt') && !isset($colIdx['jabatan'])) $colIdx['jabatan'] = $idx;
            elseif (str_contains($col, 'tmt jabatan') && !isset($colIdx['tmt_jabatan'])) $colIdx['tmt_jabatan'] = $idx;
            elseif (str_contains($col, 'satuan') && !isset($colIdx['satuan'])) $colIdx['satuan'] = $idx;
            elseif ((str_contains($col, 'tmt tni') || str_contains($col, 'tmt pns') || (str_contains($col, 'tmt') && !str_contains($col, 'pangkat') && !str_contains($col, 'jabatan') && !str_contains($col, 'gol'))) && !isset($colIdx['tmt_tni_pa'])) $colIdx['tmt_tni_pa'] = $idx;
            elseif (str_contains($col, 'agama') && !isset($colIdx['agama'])) $colIdx['agama'] = $idx;
            elseif (str_contains($col, 'suku') && !isset($colIdx['suku'])) $colIdx['suku'] = $idx;
            elseif (str_contains($col, 'lahir') && (str_contains($col, 'tgl') || str_contains($col, 'tanggal') || str_contains($col, 'waktu'))) $colIdx['tgl_lahir'] = $idx;
            elseif (str_contains($col, 'lahir') && (str_contains($col, 'tempat') || str_contains($col, 'tmp') || str_contains($col, 'tempt'))) $colIdx['tempat_lahir'] = $idx;
            elseif (str_contains($col, 'mkg') && !isset($colIdx['mkg'])) $colIdx['mkg'] = $idx;
            elseif ((str_contains($col, 'kelamin') || str_contains($col, 'jk') || str_contains($col, 'kln')) && !isset($colIdx['jenis_kelamin'])) $colIdx['jenis_kelamin'] = $idx;
            
            // PENDIDIKAN
            elseif ((str_contains($col, 'dikum') || str_contains($col, 'pendidikan umum') || (str_contains($col, 'pendidikan') && !str_contains($col, 'lanjutan')) || str_contains($col, 'dik ti') || $col === 'ti') && !isset($colIdx['dikum_ti'])) $colIdx['dikum_ti'] = $idx;
            elseif ((str_contains($col, 'dikmit') || str_contains($col, 'dikmil') || str_contains($col, 'diklat') || str_contains($col, 'pertama') || str_contains($col, 'dik ma')) && !str_contains($col, 'dikmil ti') && !isset($colIdx['dikmit_tni'])) $colIdx['dikmit_tni'] = $idx;
            elseif ((str_contains($col, 'lanjutan') || str_contains($col, 'dikmil ti')) && !isset($colIdx['pendidikan_lanjutan'])) $colIdx['pendidikan_lanjutan'] = $idx;
            
            // Context-aware THN LULUS mapping (karena judul kolomnya semua sama cuma "THN LULUS")
            elseif (str_contains($col, 'lulus') || str_contains($col, 'lls') || str_contains($col, 'thn')) {
                if (isset($colIdx['pendidikan_lanjutan']) && !isset($colIdx['tahun_lulus_lanjutan'])) {
                    $colIdx['tahun_lulus_lanjutan'] = $idx;
                } elseif (isset($colIdx['dikmit_tni']) && !isset($colIdx['thn_lulus_dikmit'])) {
                    $colIdx['thn_lulus_dikmit'] = $idx;
                } elseif (isset($colIdx['dikum_ti']) && !isset($colIdx['thn_lulus_dikum'])) {
                    $colIdx['thn_lulus_dikum'] = $idx;
                }
            }
            
            elseif (str_contains($col, 'ket') && !isset($colIdx['ket'])) $colIdx['ket'] = $idx;
        }

        $dataStartIdx = $numIdx !== null ? $numIdx + 1 : $endMerge + 1;
        $rawParsedRows = array_slice($rawRows, $dataStartIdx);

        // Gabungkan baris data yang terpisah menjadi beberapa baris (jika NO kosong, gabungkan dengan baris sebelumnya)
        $dataRows = [];
        $currentRow = null;
        foreach ($rawParsedRows as $row) {
            $no = trim($row[0] ?? '');
            // Skip baris yang benar-benar kosong semua
            $isEmptyRow = true;
            foreach ($row as $val) {
                if (trim($val ?? '') !== '') {
                    $isEmptyRow = false;
                    break;
                }
            }
            if ($isEmptyRow) continue;

            if ($no !== '' && is_numeric($no)) {
                if ($currentRow !== null) $dataRows[] = $currentRow;
                $currentRow = $row;
            } else {
                if ($currentRow !== null) {
                    foreach ($row as $c => $val) {
                        $val = trim($val ?? '');
                        if ($val !== '') {
                            $currentRow[$c] = trim(($currentRow[$c] ?? '') . "\n" . $val);
                        }
                    }
                }
            }
        }
        if ($currentRow !== null) $dataRows[] = $currentRow;

        $preview     = [];
        $skipped     = [];
        $duplicates  = [];

        foreach ($dataRows as $i => $row) {
            $row = array_values($row);
            
            // Kolom wajib
            $nrpIdx = $colIdx['nrp_nip'] ?? null;
            $namaIdx = $colIdx['nama'] ?? null;

            if ($nrpIdx === null || $namaIdx === null) continue;

            $nrp = trim($row[$nrpIdx] ?? '');
            $nama = trim($row[$namaIdx] ?? '');

            // Skip baris kosong
            if (empty($nrp) || empty($nama)) continue;
            
            $nrpLow = strtolower($nrp);
            if (in_array($nrpLow, ['nrp', 'nip', 'nrp / nip', 'nrp/nip', 'no', 'nomor'])) continue;

            $isDuplicate = Personel::where('nrp_nip', $nrp)->exists();

            $mapped = $this->mapRowToDataDynamic($row, $jenis, $colIdx);

            if ($isDuplicate) {
                $mapped['_duplicate'] = true;
                $duplicates[] = $mapped;
            } else {
                $preview[] = $mapped;
            }
        }

        // Simpan ke session untuk konfirmasi
        Session::put('import_preview_data',  $preview);
        Session::put('import_preview_jenis', $jenis);

        return view('admin.personel.import_preview', compact('preview', 'duplicates', 'jenis'));
    }

    // ════════════════════════════════════════
    //  IMPORT — Step 3: Konfirmasi & simpan
    // ════════════════════════════════════════
    public function importConfirm(Request $request)
    {
        $rows  = Session::get('import_preview_data', []);
        $jenis = Session::get('import_preview_jenis', 'militer');

        if (empty($rows)) {
            return redirect()->route('admin.personel.import_form')
                ->with('error', 'Sesi preview sudah habis. Silakan upload ulang.');
        }

        $successCount = 0;
        $errorRows    = [];

        foreach ($rows as $index => $data) {
            // Skip baris yang duplikat
            if (!empty($data['_duplicate'])) continue;

            try {
                DB::transaction(function () use ($data) {
                    Personel::create(array_filter($data, fn($k) => !str_starts_with($k, '_'), ARRAY_FILTER_USE_KEY));
                });
                $successCount++;
            } catch (\Exception $e) {
                $errorRows[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        Session::forget(['import_preview_data', 'import_preview_jenis']);

        $message = "Import selesai: {$successCount} data berhasil ditambahkan.";
        if (count($errorRows) > 0) $message .= ' ' . count($errorRows) . ' baris gagal.';

        if ($successCount > 0) {
            \App\Helpers\ActivityLogger::log('Import Personel', "Mengimpor {$successCount} data personel baru jenis {$jenis}");
        }

        return redirect()->route('admin.personel.import_form')
            ->with('import_success', $successCount)
            ->with('import_errors', $errorRows)
            ->with('success', $message);
    }

    // ════════════════════════════════════════
    //  PRIVATE HELPERS
    // ════════════════════════════════════════

    /** Map dari Request ke array field personel */
    private function mapRequestToFields(Request $r): array
    {
        $pendidikanLanjutan = $r->pendidikan_lanjutan;
        $tahunLulusLanjutan = $r->tahun_lulus_lanjutan;

        if ($r->has('pendidikan_lanjutan_arr')) {
            $edus  = (array) $r->input('pendidikan_lanjutan_arr', []);
            $years = (array) $r->input('tahun_lulus_lanjutan_arr', []);

            $cleanEdus  = [];
            $cleanYears = [];

            foreach ($edus as $idx => $eduVal) {
                $edu = trim($eduVal ?? '');
                $yr  = trim($years[$idx] ?? '');
                if ($edu !== '' || $yr !== '') {
                    $cleanEdus[]  = $edu;
                    $cleanYears[] = $yr;
                }
            }

            $pendidikanLanjutan = implode("\n", $cleanEdus);
            $tahunLulusLanjutan = implode("\n", $cleanYears);
        }

        return [
            'jenis_personel'        => $r->jenis_personel,
            'kategori_personel'     => $r->kategori_personel,
            'nrp_nip'               => $r->nrp_nip,
            'nama'                  => $r->nama,
            'pangkat_golongan'      => $r->pangkat_golongan,
            'jabatan'               => $r->jabatan,
            'satuan_bagian'         => $r->satuan_bagian ?: 'Bengpuskomlekad',
            'organization_unit_id'  => $r->organization_unit_id,
            'no_hp'                 => $r->no_hp,
            'email'                 => $r->email,
            'tmt_pangkat'           => $r->tmt_pangkat,
            'corps'                 => $r->corps,
            'tmt_jabatan'           => $r->tmt_jabatan,
            'tmt_tni_pa'            => $r->tmt_tni_pa,
            'mkg'                   => $r->mkg,
            'agama_suku'            => $r->agama_suku,
            'tgl_lahir'             => $r->tgl_lahir ?: null,
            'tempat_lahir'          => $r->tempat_lahir,
            'jenis_kelamin'         => $r->jenis_kelamin,
            'status_pernikahan'     => $r->status_pernikahan,
            'dikum_ti'              => $r->dikum_ti,
            'thn_lulus_dikum'       => $r->thn_lulus_dikum,
            'dik_pertama_tni'       => $r->dik_pertama_tni,
            'thn_lulus_dik_pertama' => $r->thn_lulus_dik_pertama,
            'dikmit_tni'            => $r->dikmit_tni,
            'thn_lulus_dikmit'      => $r->thn_lulus_dikmit,
            'pendidikan_lanjutan'   => $pendidikanLanjutan,
            'tahun_lulus_lanjutan'  => $tahunLulusLanjutan,
            'ket'                   => $r->ket,
        ];
    }

    /**
     * Map satu baris Excel ke array data personel dengan Index Dinamis
     */
    private function mapRowToDataDynamic(array $row, string $jenis, array $colIdx): array
    {
        $tglRaw  = trim($row[$colIdx['tgl_lahir'] ?? -1] ?? '');
        $tglLahir = null;
        if (!empty($tglRaw)) {
            try {
                // Handle various date formats if possible
                $tglLahir = \Carbon\Carbon::parse($tglRaw)->format('Y-m-d');
            } catch (\Exception) {
                $tglLahir = null;
            }
        }

        // Gabungkan suku + agama dalam satu kolom (Suku dulu baru Agama)
        $suku  = trim($row[$colIdx['suku'] ?? -1] ?? '');
        $agama = trim($row[$colIdx['agama'] ?? -1] ?? '');
        $agamaSuku = trim(($suku ? $suku . "\n" : '') . $agama);

        // Standarisasi Jenis Kelamin (Pria / Wanita)
        $jkRaw = trim($row[$colIdx['jenis_kelamin'] ?? -1] ?? '');
        $jkLower = strtolower($jkRaw);
        if (str_contains($jkLower, 'laki') || str_contains($jkLower, 'pria') || $jkLower === 'l') {
            $jk = 'Pria';
        } elseif (str_contains($jkLower, 'perempuan') || str_contains($jkLower, 'wanita') || $jkLower === 'w' || $jkLower === 'p') {
            $jk = 'Wanita';
        } else {
            $nrpNip = trim($row[$colIdx['nrp_nip'] ?? -1] ?? '');
            $nipClean = preg_replace('/[^0-9]/', '', $nrpNip);
            if (strlen($nipClean) >= 15 && substr($nipClean, 14, 1) === '2') {
                $jk = 'Wanita';
            } else {
                $jk = 'Pria';
            }
        }

        return [
            'jenis_personel'        => $jenis,
            'nrp_nip'               => trim($row[$colIdx['nrp_nip'] ?? -1] ?? ''),
            'nama'                  => trim($row[$colIdx['nama'] ?? -1] ?? ''),
            'pangkat_golongan'      => trim($row[$colIdx['pangkat'] ?? -1] ?? '-'),
            'tmt_pangkat'           => trim($row[$colIdx['tmt_pangkat'] ?? -1] ?? ''),
            'corps'                 => trim($row[$colIdx['corps'] ?? -1] ?? ''),
            'jabatan'               => trim($row[$colIdx['jabatan'] ?? -1] ?? '-'),
            'tmt_jabatan'           => trim($row[$colIdx['tmt_jabatan'] ?? -1] ?? ''),
            'satuan_bagian'         => trim($row[$colIdx['satuan'] ?? -1] ?? '') ?: 'Bengpuskomlekad',
            'tmt_tni_pa'            => trim($row[$colIdx['tmt_tni_pa'] ?? -1] ?? ''),
            'agama_suku'            => $agamaSuku ?: null,
            'tgl_lahir'             => $tglLahir,
            'tempat_lahir'          => trim($row[$colIdx['tempat_lahir'] ?? -1] ?? ''),
            'mkg'                   => trim($row[$colIdx['mkg'] ?? -1] ?? ''),
            'jenis_kelamin'         => $jk,
            'dikum_ti'              => trim($row[$colIdx['dikum_ti'] ?? -1] ?? ''),
            'thn_lulus_dikum'       => trim($row[$colIdx['thn_lulus_dikum'] ?? -1] ?? ''),
            'dikmit_tni'            => trim($row[$colIdx['dikmit_tni'] ?? -1] ?? ''),
            'thn_lulus_dikmit'      => trim($row[$colIdx['thn_lulus_dikmit'] ?? -1] ?? ''),
            'pendidikan_lanjutan'   => trim($row[$colIdx['pendidikan_lanjutan'] ?? -1] ?? ''),
            'tahun_lulus_lanjutan'  => trim($row[$colIdx['tahun_lulus_lanjutan'] ?? -1] ?? ''),
            'ket'                   => trim($row[$colIdx['ket'] ?? -1] ?? ''),
            'status_aktif'          => true,
        ];
    }
}
