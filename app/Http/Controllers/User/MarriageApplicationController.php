<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\MarriageApplication;
use App\Models\MarriagePartner;
use App\Models\MarriageStatusHistory;
use App\Models\MarriageDocumentType;
use App\Models\MarriageLetter;
use App\Services\MarriageLetterGenerator;

class MarriageApplicationController extends Controller
{
    // Documents are fetched dynamically from MarriageDocumentType.

    // ─── Normalize jenis_kelamin and resolve roles ──────
    private function resolvePeran(?string $jenisKelamin): array
    {
        if ($jenisKelamin === 'Pria') {
            return ['suami', 'istri', 'Wanita'];
        }
        if ($jenisKelamin === 'Wanita') {
            return ['istri', 'suami', 'Pria'];
        }
        return [null, null, null];
    }

    // ─── index ───────────────────────────────────────────
    public function index()
    {
        $applications = Auth::user()
            ->marriageApplications()
            ->with('partner')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.marriage_applications.index', compact('applications'));
    }

    public function create()
    {
        $user = Auth::user();
        $personel = $user->personel;

        if (!$personel) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Akun Anda belum terhubung dengan data Personel. Silakan hubungi Admin Personalia.');
        }

        if (empty($personel->jenis_kelamin)) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Data jenis kelamin pada profil personel Anda belum lengkap. Silakan hubungi Admin Personalia.');
        }

        if ($personel->status_pernikahan === 'Menikah') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Status pernikahan Anda saat ini adalah Menikah. Anda tidak dapat membuat pengajuan nikah baru.');
        }

        [$peranAnggota, $peranPasangan, $genderPasangan] = $this->resolvePeran($personel->jenis_kelamin);

        if (!$peranAnggota) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Nilai jenis kelamin Personel tidak valid. Harap hubungi Admin Personalia.');
        }

        return view('user.marriage_applications.create', compact('personel', 'peranAnggota', 'peranPasangan', 'genderPasangan'));
    }

    // ─── saveDraft ───────────────────────────────────────
    public function saveDraft(Request $request)
    {
        $user = Auth::user();
        $personel = $user->personel;

        if (!$personel) {
            return redirect()->route('user.dashboard')->with('error', 'Akun Anda belum terhubung dengan data Personel.');
        }

        if (empty($personel->jenis_kelamin)) {
            return redirect()->route('user.dashboard')->with('error', 'Data jenis kelamin pada profil personel Anda belum lengkap.');
        }

        if ($personel->status_pernikahan === 'Menikah') {
            return redirect()->route('user.dashboard')->with('error', 'Status pernikahan Anda saat ini adalah Menikah. Anda tidak dapat membuat pengajuan nikah baru.');
        }

        [$peranAnggota, $peranPasangan, $genderPasangan] = $this->resolvePeran($personel->jenis_kelamin);
        if (!$peranAnggota) {
            return redirect()->route('user.dashboard')->with('error', 'Data jenis kelamin Personel tidak valid.');
        }

        $application = MarriageApplication::create([
            'user_id'              => $user->id,
            'personel_id'          => $personel->id,
            'jenis_kelamin_anggota'=> $personel->jenis_kelamin,
            'peran_anggota'        => $peranAnggota,
            'tanggal_pengajuan'    => now(),
            'tanggal_rencana_nikah'=> $request->tanggal_rencana_nikah ?: now()->addMonth(),
            'tempat_nikah'         => $request->tempat_nikah ?: '-',
            'alamat_nikah'         => $request->alamat_nikah ?: '-',
            'kelurahan_nikah'      => $request->kelurahan_nikah ?: '-',
            'kecamatan_nikah'      => $request->kecamatan_nikah ?: '-',
            'kabupaten_nikah'      => $request->kabupaten_nikah ?: '-',
            'provinsi_nikah'       => $request->provinsi_nikah ?: '-',
            'alamat_domisili'      => $request->alamat_domisili ?: '-',
            'kelurahan_domisili'   => $request->kelurahan_domisili ?: '-',
            'kecamatan_domisili'   => $request->kecamatan_domisili ?: '-',
            'kabupaten_domisili'   => $request->kabupaten_domisili ?: '-',
            'provinsi_domisili'    => $request->provinsi_domisili ?: '-',
            'kua_tujuan'           => $request->kua_tujuan ?: '-',
            'status'               => 'DRAFT',
        ]);

        // Save partner data if partial is provided
        if ($request->filled('pasangan_nama')) {
            MarriagePartner::create([
                'marriage_application_id' => $application->id,
                'peran'           => $peranPasangan,
                'nama'            => $request->pasangan_nama ?: '-',
                'tempat_lahir'    => $request->pasangan_tempat_lahir ?: '-',
                'tanggal_lahir'   => $request->pasangan_tanggal_lahir ?: now(),
                'pekerjaan'       => $request->pasangan_pekerjaan ?: '-',
                'status_pekerjaan'=> $request->pasangan_status_pekerjaan ?: 'Non-ASN',
                'instansi'        => $request->pasangan_instansi,
                'jabatan'         => $request->pasangan_jabatan,
                'agama'           => $request->pasangan_agama ?: '-',
                'suku'            => $request->pasangan_suku ?: '-',
                'alamat'          => $request->pasangan_alamat ?: '-',
                'kelurahan'       => $request->pasangan_kelurahan ?: '-',
                'kecamatan'       => $request->pasangan_kecamatan ?: '-',
                'kabupaten'       => $request->pasangan_kabupaten ?: '-',
                'provinsi'        => $request->pasangan_provinsi ?: '-',
                'bapak_nama'      => $request->bapak_nama ?: '-',
                'bapak_agama'     => $request->bapak_agama ?: '-',
                'bapak_pekerjaan' => $request->bapak_pekerjaan ?: '-',
                'bapak_alamat'    => $request->bapak_alamat ?: '-',
                'ibu_nama'        => $request->ibu_nama ?: '-',
                'ibu_agama'       => $request->ibu_agama ?: '-',
                'ibu_pekerjaan'   => $request->ibu_pekerjaan ?: '-',
                'ibu_alamat'      => $request->ibu_alamat ?: '-',
            ]);
        }

        MarriageStatusHistory::create([
            'marriage_application_id' => $application->id,
            'status'     => 'DRAFT',
            'catatan'    => 'Draft pengajuan nikah disimpan oleh anggota.',
            'changed_by' => $user->id,
        ]);

        return redirect()->route('user.pengajuan_nikah.show', $application->id)
            ->with('success', 'Draft berhasil disimpan. Lengkapi data sebelum mengajukan ke Admin.');
    }

    // ─── store (final submit) ────────────────────────────
    public function store(Request $request)
    {
        $user = Auth::user();
        $personel = $user->personel;

        if (!$personel) {
            return redirect()->route('user.dashboard')->with('error', 'Akun Anda belum terhubung dengan data Personel.');
        }

        if (empty($personel->jenis_kelamin)) {
            return redirect()->route('user.dashboard')->with('error', 'Data jenis kelamin pada profil personel Anda belum lengkap.');
        }

        if ($personel->status_pernikahan === 'Menikah') {
            return redirect()->route('user.dashboard')->with('error', 'Status pernikahan Anda saat ini adalah Menikah. Anda tidak dapat membuat pengajuan nikah baru.');
        }

        [$peranAnggota, $peranPasangan, $genderPasangan] = $this->resolvePeran($personel->jenis_kelamin);
        if (!$peranAnggota) {
            return redirect()->route('user.dashboard')->with('error', 'Data jenis kelamin Personel tidak valid.');
        }

        $validated = $request->validate([
            // Pernikahan
            'tanggal_rencana_nikah' => 'required|date',
            'tempat_nikah'          => 'required|string|max:255',
            'alamat_nikah'          => 'required|string|max:500',
            'kelurahan_nikah'       => 'required|string|max:100',
            'kecamatan_nikah'       => 'required|string|max:100',
            'kabupaten_nikah'       => 'required|string|max:100',
            'provinsi_nikah'        => 'required|string|max:100',
            // Pasangan
            'pasangan_nama'             => 'required|string|max:255',
            'pasangan_tempat_lahir'     => 'required|string|max:100',
            'pasangan_tanggal_lahir'    => 'required|date',
            'pasangan_pekerjaan'        => 'required|string|max:255',
            'pasangan_status_pekerjaan' => 'required|in:ASN,Non-ASN',
            'pasangan_instansi'         => 'nullable|required_if:pasangan_status_pekerjaan,ASN|string|max:255',
            'pasangan_jabatan'          => 'nullable|required_if:pasangan_status_pekerjaan,ASN|string|max:255',
            'pasangan_agama'            => 'required|string|max:100',
            'pasangan_suku'             => 'required|string|max:100',
            'pasangan_alamat'           => 'required|string|max:500',
            'pasangan_kelurahan'        => 'required|string|max:100',
            'pasangan_kecamatan'        => 'required|string|max:100',
            'pasangan_kabupaten'        => 'required|string|max:100',
            'pasangan_provinsi'         => 'required|string|max:100',
            // Domisili
            'alamat_domisili'           => 'required|string|max:500',
            'kelurahan_domisili'        => 'required|string|max:100',
            'kecamatan_domisili'        => 'required|string|max:100',
            'kabupaten_domisili'        => 'required|string|max:100',
            'provinsi_domisili'         => 'required|string|max:100',
            'kua_tujuan'                => 'required|string|max:100',
            // Orang Tua
            'bapak_nama'      => 'required|string|max:255',
            'bapak_agama'     => 'required|string|max:100',
            'bapak_pekerjaan' => 'required|string|max:255',
            'bapak_alamat'    => 'required|string|max:500',
            'ibu_nama'        => 'required|string|max:255',
            'ibu_agama'       => 'required|string|max:100',
            'ibu_pekerjaan'   => 'required|string|max:255',
            'ibu_alamat'      => 'required|string|max:500',
        ]);

        $application = MarriageApplication::create([
            'user_id'               => $user->id,
            'personel_id'           => $personel->id,
            'jenis_kelamin_anggota' => $personel->jenis_kelamin,
            'peran_anggota'         => $peranAnggota,
            'tanggal_pengajuan'     => now(),
            'tanggal_rencana_nikah' => $validated['tanggal_rencana_nikah'],
            'tempat_nikah'          => $validated['tempat_nikah'],
            'alamat_nikah'          => $validated['alamat_nikah'],
            'kelurahan_nikah'       => $validated['kelurahan_nikah'],
            'kecamatan_nikah'       => $validated['kecamatan_nikah'],
            'kabupaten_nikah'       => $validated['kabupaten_nikah'],
            'provinsi_nikah'        => $validated['provinsi_nikah'],
            'alamat_domisili'       => $validated['alamat_domisili'],
            'kelurahan_domisili'    => $validated['kelurahan_domisili'],
            'kecamatan_domisili'    => $validated['kecamatan_domisili'],
            'kabupaten_domisili'    => $validated['kabupaten_domisili'],
            'provinsi_domisili'     => $validated['provinsi_domisili'],
            'kua_tujuan'            => $validated['kua_tujuan'],
            'status'                => 'DIAJUKAN',
        ]);

        MarriagePartner::create([
            'marriage_application_id' => $application->id,
            'peran'            => $peranPasangan,
            'nama'             => $validated['pasangan_nama'],
            'tempat_lahir'     => $validated['pasangan_tempat_lahir'],
            'tanggal_lahir'    => $validated['pasangan_tanggal_lahir'],
            'pekerjaan'        => $validated['pasangan_pekerjaan'],
            'status_pekerjaan' => $validated['pasangan_status_pekerjaan'],
            'instansi'         => $request->pasangan_instansi,
            'jabatan'          => $request->pasangan_jabatan,
            'agama'            => $validated['pasangan_agama'],
            'suku'             => $validated['pasangan_suku'],
            'alamat'           => $validated['pasangan_alamat'],
            'kelurahan'        => $validated['pasangan_kelurahan'],
            'kecamatan'        => $validated['pasangan_kecamatan'],
            'kabupaten'        => $validated['pasangan_kabupaten'],
            'provinsi'         => $validated['pasangan_provinsi'],
            'bapak_nama'       => $validated['bapak_nama'],
            'bapak_agama'      => $validated['bapak_agama'],
            'bapak_pekerjaan'  => $validated['bapak_pekerjaan'],
            'bapak_alamat'     => $validated['bapak_alamat'],
            'ibu_nama'         => $validated['ibu_nama'],
            'ibu_agama'        => $validated['ibu_agama'],
            'ibu_pekerjaan'    => $validated['ibu_pekerjaan'],
            'ibu_alamat'       => $validated['ibu_alamat'],
        ]);

        MarriageStatusHistory::create([
            'marriage_application_id' => $application->id,
            'status'     => 'DIAJUKAN',
            'catatan'    => 'Pengajuan nikah disubmit oleh anggota dan menunggu verifikasi Admin.',
            'changed_by' => $user->id,
        ]);

        return redirect()->route('user.pengajuan_nikah.show', $application->id)
            ->with('success', 'Pengajuan berhasil dibuat dengan status DIAJUKAN. Segera unggah dokumen persyaratan.');
    }

    // ─── submit (DRAFT → DIAJUKAN) ───────────────────────
    public function submit(Request $request, $id)
    {
        $application = Auth::user()->marriageApplications()->with(['partner', 'documents'])->findOrFail($id);

        if ($application->status !== MarriageApplication::STATUS_DRAFT && $application->status !== MarriageApplication::STATUS_DITOLAK) {
            return redirect()->back()->with('error', 'Hanya pengajuan berstatus DRAFT atau DITOLAK yang dapat disubmit.');
        }

        if (!$application->partner) {
            return redirect()->back()->with('error', 'Data pasangan belum diisi. Lengkapi form sebelum mengajukan.');
        }

        $application->update(['status' => 'DIAJUKAN']);

        MarriageStatusHistory::create([
            'marriage_application_id' => $application->id,
            'status'     => 'DIAJUKAN',
            'catatan'    => 'Pengajuan disubmit ke Admin untuk diverifikasi.',
            'changed_by' => Auth::id(),
        ]);

        return redirect()->route('user.pengajuan_nikah.show', $application->id)
            ->with('success', 'Pengajuan berhasil disubmit. Admin akan segera memverifikasi dokumen Anda.');
    }

    // ─── show ────────────────────────────────────────────
    public function show($id)
    {
        $application = Auth::user()
            ->marriageApplications()
            ->with(['partner', 'documents', 'statusHistories.changer', 'letters'])
            ->findOrFail($id);

        // ─── Document Requirements ─────────────────────────────
        $docTypes = MarriageDocumentType::where(function ($q) {
                $q->whereNotIn('category', ['SURAT_SATUAN', 'SURAT_FINAL'])
                  ->orWhere('code', 'SURAT_PERMOHONAN_IZIN_NIKAH');
            })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $requiredAnggota = $docTypes->filter(fn($dt) => $dt->owner_type === 'ANGGOTA' || $dt->owner_type === 'ORANG_TUA_ANGGOTA' || $dt->code === 'SURAT_PERMOHONAN_IZIN_NIKAH');
        
        $requiredPasangan = $docTypes->filter(function($dt) use ($application) {
            if ($dt->owner_type !== 'PASANGAN' && $dt->owner_type !== 'ORANG_TUA_PASANGAN') {
                return false;
            }
            if ($dt->code === 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN') {
                return $application->partner && $application->partner->status_pekerjaan === 'ASN';
            }
            return true;
        });

        $coverLetters = \App\Models\MarriageDocumentType::whereIn('code', [
            'PENGANTAR_NA', 'PENGANTAR_KESDAM', 'PENGANTAR_BINTALDAM', 'PENGANTAR_LITPERS', 'PENGANTAR_SKBD'
        ])->where('is_active', true)->orderBy('sort_order')->get();

        return view('user.marriage_applications.show', compact('application', 'requiredAnggota', 'requiredPasangan', 'coverLetters'));
    }

    // ─── uploadDocument ──────────────────────────────────
    public function uploadDocument(Request $request, $id)
    {
        $application = Auth::user()->marriageApplications()->findOrFail($id);

        if (!in_array($application->status, [MarriageApplication::STATUS_DRAFT, MarriageApplication::STATUS_DIAJUKAN, MarriageApplication::STATUS_PERLU_PERBAIKAN])) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diunggah pada status ' . $application->status . '.');
        }

        $request->validate([
            'pihak'        => 'required|in:Anggota,Pasangan',
            'marriage_document_type_id' => 'required|exists:marriage_document_types,id',
            'jenis_dokumen'=> 'required|string|max:255',
            'file'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $docType = MarriageDocumentType::findOrFail($request->marriage_document_type_id);
        $document = $application->documents()->where('marriage_document_type_id', $docType->id)->first();

        // Delete old file if exists physically (do this before storing the new one in case name overlaps)
        if ($document && Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $file    = $request->file('file');
        
        // [Application_ID]_[Timestamp]_[Safe_Document_Code].[Extension]
        $safeCode = preg_replace('/[^A-Za-z0-9_.\-]/', '_', $docType->code);
        $fileName = $application->id . '_' . now()->timestamp . '_' . $safeCode . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('marriage_documents/' . $application->id, $fileName, 'private');

        if ($document) {
            // Check if document is already accepted
            if ($document->status_verifikasi === 'DITERIMA') {
                return redirect()->back()->with('error', 'Dokumen yang sudah DITERIMA tidak dapat diubah.');
            }

            // Keep old record — store revision as updated record
            $document->update([
                'file_path'          => $filePath,
                'file_name'          => $fileName,
                'mime_type'          => $file->getClientMimeType(),
                'file_size'          => $file->getSize(),
                'status_verifikasi'  => 'BELUM_DIPERIKSA',
                'catatan_verifikasi' => null,
                'uploaded_at'        => now(),
            ]);
        } else {
            $application->documents()->create([
                'marriage_document_type_id' => $docType->id,
                'pihak'        => $request->pihak,
                'jenis_dokumen'=> $request->jenis_dokumen, // legacy string code
                'nama_dokumen' => $docType->name,
                'file_path'    => $filePath,
                'file_name'    => $fileName,
                'mime_type'    => $file->getClientMimeType(),
                'file_size'    => $file->getSize(),
                'status_verifikasi' => 'BELUM_DIPERIKSA',
                'uploaded_at'  => now(),
            ]);
        }



        return redirect()->back()->with('success', 'Dokumen "' . $request->jenis_dokumen . '" berhasil diunggah.');
    }

    // ─── downloadDocument ────────────────────────────────
    public function downloadDocument($id, $docId)
    {
        // Security: ensure the application belongs to the current user
        $application = Auth::user()->marriageApplications()->findOrFail($id);
        $document    = $application->documents()->findOrFail($docId);

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }

    // ─── generateLetter ──────────────────────────────────
    public function generateLetter(Request $request, $id, MarriageLetterGenerator $generator)
    {
        $application = Auth::user()->marriageApplications()->findOrFail($id);
        
        $request->validate([
            'type_code' => 'required|string'
        ]);
        
        $typeCode = $request->type_code;

        $allowedStatusesForLetter = [
            MarriageApplication::STATUS_PENGAJUAN_DISETUJUI,
            MarriageApplication::STATUS_PERLU_PERBAIKAN,
            MarriageApplication::STATUS_DIVERIFIKASI,
            MarriageApplication::STATUS_DISETUJUI,
            MarriageApplication::STATUS_SELESAI
        ];

        if ($typeCode === 'SURAT_PERMOHONAN_IZIN_NIKAH') {
            $allowedStatusesForLetter[] = MarriageApplication::STATUS_DRAFT;
            $allowedStatusesForLetter[] = MarriageApplication::STATUS_DIAJUKAN;
        }

        if ($typeCode === 'SURAT_IZIN_NIKAH_FINAL' && !in_array($application->status, [MarriageApplication::STATUS_DISETUJUI, MarriageApplication::STATUS_SELESAI])) {
            return redirect()->back()->with('error', 'Surat Izin Nikah final hanya dapat digenerate setelah pengajuan disetujui akhir (DISETUJUI).');
        }

        if ($typeCode !== 'SURAT_IZIN_NIKAH_FINAL' && !in_array($application->status, $allowedStatusesForLetter)) {
            return redirect()->back()->with('error', 'Surat pengantar hanya dapat digenerate setelah pengajuan awal disetujui Admin.');
        }
        
        $documentType = MarriageDocumentType::where('code', $typeCode)->firstOrFail();
        
        if (!$documentType->template_path) {
            return redirect()->back()->with('error', 'Template untuk surat ini belum tersedia.');
        }

        $templateAbsolutePath = storage_path('app/' . $documentType->template_path);
        
        if (!file_exists($templateAbsolutePath)) {
            return redirect()->back()->with('error', 'File template fisik tidak ditemukan: ' . $documentType->template_path);
        }

        $outputName = strtolower($typeCode) . '_' . $application->id;
        
        try {
            $filePath = $generator->generate($application, $templateAbsolutePath, $outputName);
            
            MarriageLetter::updateOrCreate(
                [
                    'marriage_application_id' => $application->id,
                    'jenis_surat'             => $typeCode,
                ],
                [
                    'file_generated' => $filePath,
                    'status'         => 'TERSEDIA',
                    'generated_at'   => now(),
                    'generated_by'   => Auth::id(),
                ]
            );
            
            return redirect()->back()->with('success', 'Surat ' . $documentType->name . ' berhasil di-generate.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate surat: ' . $e->getMessage());
        }
    }

    // ─── downloadLetter ──────────────────────────────────
    public function downloadLetter($id, $letterId)
    {
        $application = Auth::user()->marriageApplications()->findOrFail($id);
        $letter      = $application->letters()->findOrFail($letterId);
        
        $allowedStatusesForLetter = [
            MarriageApplication::STATUS_PENGAJUAN_DISETUJUI,
            MarriageApplication::STATUS_PERLU_PERBAIKAN,
            MarriageApplication::STATUS_DIVERIFIKASI,
            MarriageApplication::STATUS_DISETUJUI,
            MarriageApplication::STATUS_SELESAI
        ];

        if ($letter->jenis_surat === 'SURAT_PERMOHONAN_IZIN_NIKAH') {
            $allowedStatusesForLetter[] = MarriageApplication::STATUS_DRAFT;
            $allowedStatusesForLetter[] = MarriageApplication::STATUS_DIAJUKAN;
        }

        if ($letter->jenis_surat === 'SURAT_IZIN_NIKAH_FINAL' && !in_array($application->status, [MarriageApplication::STATUS_DISETUJUI, MarriageApplication::STATUS_SELESAI])) {
            abort(403, 'Akses ditolak. Surat Izin Nikah final hanya dapat diakses setelah pengajuan disetujui akhir (DISETUJUI).');
        }

        if ($letter->jenis_surat !== 'SURAT_IZIN_NIKAH_FINAL' && !in_array($application->status, $allowedStatusesForLetter)) {
            abort(403, 'Akses ditolak. Surat belum tersedia untuk status saat ini.');
        }

        if (!Storage::disk('private')->exists($letter->file_generated)) {
            abort(404, 'File surat tidak ditemukan.');
        }
        
        $fileName = basename($letter->file_generated);
        return Storage::disk('private')->download($letter->file_generated, $fileName);
    }
}
