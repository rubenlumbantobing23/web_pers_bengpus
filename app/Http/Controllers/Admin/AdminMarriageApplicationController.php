<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\MarriageApplication;
use App\Models\MarriageDocument;
use App\Models\MarriageStatusHistory;
use App\Models\MarriageLetter;

class AdminMarriageApplicationController extends Controller
{
    // ─── index ───────────────────────────────────────────
    public function index()
    {
        $applications = MarriageApplication::with(['user', 'personel', 'partner'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.marriage_applications.index', compact('applications'));
    }

    // ─── show ────────────────────────────────────────────
    public function show($id)
    {
        $application = MarriageApplication::with([
            'user',
            'personel',
            'partner',
            'documents',
            'statusHistories.changer',
            'letters.generator',
        ])->findOrFail($id);

        // ─── Document Requirements ─────────────────────────────
        $docTypes = \App\Models\MarriageDocumentType::whereNotIn('category', ['SURAT_SATUAN', 'SURAT_FINAL'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $requiredAnggota = $docTypes->filter(fn($dt) => $dt->owner_type === 'ANGGOTA' || $dt->owner_type === 'ORANG_TUA_ANGGOTA');
        
        $requiredPasangan = $docTypes->filter(function($dt) use ($application) {
            if ($dt->owner_type !== 'PASANGAN' && $dt->owner_type !== 'ORANG_TUA_PASANGAN') {
                return false;
            }
            if ($dt->code === 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN') {
                return $application->partner && $application->partner->status_pekerjaan === 'ASN';
            }
            return true;
        });

        return view('admin.marriage_applications.show', compact('application', 'requiredAnggota', 'requiredPasangan'));
    }

    // ─── verifyDocument ──────────────────────────────────
    public function verifyDocument(Request $request, $id)
    {
        $request->validate([
            'document_id' => 'required|exists:marriage_documents,id',
            'status'      => 'required|in:DITERIMA,DITOLAK',
            'catatan'     => 'nullable|string|max:1000',
        ]);

        $application = MarriageApplication::findOrFail($id);
        $document    = $application->documents()->findOrFail($request->document_id);

        $document->update([
            'status_verifikasi'  => $request->status,
            'catatan_verifikasi' => $request->catatan,
            'verified_at'        => now(),
            'verified_by'        => Auth::id(),
        ]);

        // If document rejected, auto-change application status to PERLU_PERBAIKAN
        if ($request->status === 'DITOLAK' && $application->status !== 'PERLU_PERBAIKAN') {
            $application->update(['status' => 'PERLU_PERBAIKAN']);
            MarriageStatusHistory::create([
                'marriage_application_id' => $application->id,
                'status'     => 'PERLU_PERBAIKAN',
                'catatan'    => 'Dokumen "' . $document->jenis_dokumen . '" ditolak. Anggota diminta memperbaiki dokumen tersebut.' . ($request->catatan ? ' Catatan: ' . $request->catatan : ''),
                'changed_by' => Auth::id(),
            ]);
        }

        $action = $request->status === 'DITERIMA' ? 'diterima' : 'ditolak';
        return redirect()->back()->with('success', 'Dokumen "' . $document->jenis_dokumen . '" berhasil ' . $action . '.');
    }

    // ─── updateStatus ────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:DIVERIFIKASI,DISETUJUI,DITOLAK,SELESAI',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $application = MarriageApplication::findOrFail($id);

        // Guard: cannot move to DIVERIFIKASI if not all required docs are present and DITERIMA
        if ($request->status === 'DIVERIFIKASI') {
            $docTypes = \App\Models\MarriageDocumentType::whereNotIn('category', ['SURAT_SATUAN', 'SURAT_FINAL'])
                ->where('is_active', true)
                ->get();
            
            $requiredDocs = $docTypes->filter(function($dt) use ($application) {
                if ($dt->code === 'SURAT_KETERANGAN_DINAS_CALON_PASANGAN') {
                    return $application->partner && $application->partner->status_pekerjaan === 'ASN';
                }
                return true;
            });

            foreach ($requiredDocs as $docType) {
                $doc = $application->documents()->where('marriage_document_type_id', $docType->id)->first();
                if (!$doc) {
                    return redirect()->back()->with('error', 'Masih terdapat dokumen wajib yang BELUM ADA. Pastikan semua dokumen diunggah dan diterima sebelum mengubah status ke DIVERIFIKASI.');
                }
                if ($doc->status_verifikasi !== 'DITERIMA') {
                    return redirect()->back()->with('error', 'Masih terdapat dokumen yang ' . $doc->status_verifikasi . '. Pastikan semua dokumen DITERIMA sebelum mengubah status ke DIVERIFIKASI.');
                }
            }
        }

        $application->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan,
        ]);

        MarriageStatusHistory::create([
            'marriage_application_id' => $application->id,
            'status'     => $request->status,
            'catatan'    => $request->catatan,
            'changed_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Status pengajuan berhasil diubah ke ' . str_replace('_', ' ', $request->status) . '.');
    }

    // ─── generateLetter ──────────────────────────────────
    public function generateLetter(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required|string',
        ]);

        $application = MarriageApplication::with(['personel', 'partner'])->findOrFail($id);

        // Map jenis_surat to template filename
        $templateMap = [
            'Surat Izin Nikah'                     => 'template_surat_izin_nikah.docx',
            'Surat Pengantar NA'                    => 'template_surat_pengantar_na.docx',
            'Surat Pengantar Pemeriksaan Kesdam'   => 'template_surat_pengantar_kesdam.docx',
            'Surat Pengantar Bintaldam'             => 'template_surat_pengantar_bintaldam.docx',
            'Surat Pengantar Litpers'               => 'template_surat_pengantar_litpers.docx',
            'Surat Permohonan SKBD'                 => 'template_surat_skbd.docx',
            'Surat Persetujuan Orang Tua/Wali'      => 'template_surat_persetujuan_ortua.docx',
            'Surat Kesanggupan Calon Pasangan'      => 'template_surat_kesanggupan_pasangan.docx',
            'Surat Keterangan Usia Calon Pasangan'  => 'template_surat_ket_usia.docx',
        ];

        $templateFile = $templateMap[$request->jenis_surat] ?? null;
        if (!$templateFile) {
            return redirect()->back()->with('error', 'Jenis surat tidak dikenali.');
        }

        $templatePath = storage_path('app/templates/' . $templateFile);

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('info',
                'Template "' . $templateFile . '" belum diunggah. Silakan upload di menu Pengaturan Template terlebih dahulu.'
            );
        }

        try {
            $generator  = new \App\Services\MarriageLetterGenerator();
            $outputName = str_replace(['/', '\\', ' '], '_', $request->jenis_surat) . '_' . str_replace(' ', '_', $application->personel->nama);
            $filePath   = $generator->generate($application, $templatePath, $outputName);

            MarriageLetter::create([
                'marriage_application_id' => $application->id,
                'jenis_surat'   => $request->jenis_surat,
                'file_generated'=> $filePath,
                'status'        => 'GENERATED',
                'generated_at'  => now(),
                'generated_by'  => Auth::id(),
            ]);

            return redirect()->back()->with('success', $request->jenis_surat . ' berhasil di-generate.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate surat: ' . $e->getMessage());
        }
    }

    // ─── downloadDocument ────────────────────────────────
    public function downloadDocument($id, $docId)
    {
        $application = MarriageApplication::findOrFail($id);
        $document    = $application->documents()->findOrFail($docId);

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }

    // ─── downloadLetter ──────────────────────────────────
    public function downloadLetter($id, $letterId)
    {
        $application = MarriageApplication::findOrFail($id);
        $letter      = $application->letters()->findOrFail($letterId);

        if (!Storage::disk('private')->exists($letter->file_generated)) {
            abort(404, 'File surat tidak ditemukan di storage.');
        }

        $downloadName = str_replace(['/', '\\', ' '], '_', $letter->jenis_surat) . '_' . $application->personel->nama . '.docx';
        return Storage::disk('private')->download($letter->file_generated, $downloadName);
    }
}
