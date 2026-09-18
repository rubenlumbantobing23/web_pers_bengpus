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

        $docTypes = \App\Models\MarriageDocumentType::where(function ($q) {
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

        return view('admin.marriage_applications.show', compact('application', 'requiredAnggota', 'requiredPasangan', 'coverLetters'));
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
            'status'  => 'required|in:PENGAJUAN_DISETUJUI,DITOLAK,DIVERIFIKASI,DISETUJUI,SELESAI',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $application = MarriageApplication::findOrFail($id);
        $newStatus = $request->status;

        // --- State Machine Transitions ---
        if ($newStatus === MarriageApplication::STATUS_PENGAJUAN_DISETUJUI) {
            if ($application->status !== MarriageApplication::STATUS_DIAJUKAN) {
                return redirect()->back()->with('error', 'Hanya pengajuan status DIAJUKAN yang dapat disetujui awal.');
            }
            
            $docTypeSPN = \App\Models\MarriageDocumentType::where('code', 'SURAT_PERMOHONAN_IZIN_NIKAH')->first();
            if ($docTypeSPN) {
                $docSPN = $application->documents()->where('marriage_document_type_id', $docTypeSPN->id)->first();
                if (!$docSPN || $docSPN->status_verifikasi !== 'DITERIMA') {
                    return redirect()->back()->with('error', 'Surat Pengajuan Nikah (signed-return) belum diunggah atau belum diverifikasi/diterima.');
                }
            }
        } elseif ($newStatus === MarriageApplication::STATUS_DITOLAK) {
            if ($application->status !== MarriageApplication::STATUS_DIAJUKAN) {
                return redirect()->back()->with('error', 'Penolakan awal hanya dapat dilakukan pada status DIAJUKAN.');
            }
            if (empty($request->catatan)) {
                return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
            }
        } elseif ($newStatus === MarriageApplication::STATUS_DIVERIFIKASI) {
            if (!in_array($application->status, [MarriageApplication::STATUS_PENGAJUAN_DISETUJUI, MarriageApplication::STATUS_PERLU_PERBAIKAN])) {
                return redirect()->back()->with('error', 'Status tidak valid untuk DIVERIFIKASI.');
            }
            
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
        } elseif ($newStatus === MarriageApplication::STATUS_DISETUJUI) {
            if ($application->status !== MarriageApplication::STATUS_DIVERIFIKASI) {
                return redirect()->back()->with('error', 'Hanya pengajuan status DIVERIFIKASI yang dapat diberikan Approval Akhir (DISETUJUI).');
            }

            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($application, $request, $newStatus) {
                    $application->update([
                        'status'        => $newStatus,
                        'catatan_admin' => $request->catatan,
                    ]);

                    $application->personel()->update([
                        'status_pernikahan' => 'Menikah'
                    ]);

                    MarriageStatusHistory::create([
                        'marriage_application_id' => $application->id,
                        'status'     => $newStatus,
                        'catatan'    => $request->catatan ?: 'Approval Akhir diberikan oleh Kabeng.',
                        'changed_by' => Auth::id(),
                    ]);
                });
                return redirect()->back()->with('success', 'Approval akhir berhasil. Status pernikahan personel telah diubah menjadi Menikah.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memproses Approval Akhir: ' . $e->getMessage());
            }

        } elseif ($newStatus === MarriageApplication::STATUS_SELESAI) {
            if ($application->status !== MarriageApplication::STATUS_DISETUJUI) {
                return redirect()->back()->with('error', 'Hanya pengajuan status DISETUJUI yang dapat diselesaikan.');
            }

            // Enforce SURAT_IZIN_NIKAH_FINAL is generated and physically exists
            $finalLetter = \App\Models\MarriageLetter::where('marriage_application_id', $application->id)
                ->where('jenis_surat', 'SURAT_IZIN_NIKAH_FINAL')
                ->where('status', 'TERSEDIA')
                ->first();

            if (!$finalLetter || !\Illuminate\Support\Facades\Storage::disk('private')->exists($finalLetter->file_generated)) {
                return redirect()->back()->with('error', 'Surat Izin Nikah final belum tersedia. Generate Surat Izin Nikah terlebih dahulu.');
            }
        } else {
            return redirect()->back()->with('error', 'Status tidak dikenali.');
        }

        // Apply update for non-DISETUJUI statuses
        if ($newStatus !== MarriageApplication::STATUS_DISETUJUI) {
            $application->update([
                'status'        => $newStatus,
                'catatan_admin' => $request->catatan,
            ]);

            MarriageStatusHistory::create([
                'marriage_application_id' => $application->id,
                'status'     => $newStatus,
                'catatan'    => $request->catatan,
                'changed_by' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'Status pengajuan berhasil diubah ke ' . str_replace('_', ' ', $newStatus) . '.');
    }

    // ─── generateLetter ──────────────────────────────────
    public function generateLetter(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required|string',
        ]);

        $application = MarriageApplication::with(['personel', 'partner'])->findOrFail($id);
        
        $allowedStatusesForLetter = [
            MarriageApplication::STATUS_PENGAJUAN_DISETUJUI,
            MarriageApplication::STATUS_PERLU_PERBAIKAN,
            MarriageApplication::STATUS_DIVERIFIKASI,
            MarriageApplication::STATUS_DISETUJUI,
            MarriageApplication::STATUS_SELESAI
        ];

        if ($request->jenis_surat === 'SURAT_PERMOHONAN_IZIN_NIKAH') {
            $allowedStatusesForLetter[] = MarriageApplication::STATUS_DRAFT;
            $allowedStatusesForLetter[] = MarriageApplication::STATUS_DIAJUKAN;
        }

        if ($request->jenis_surat === 'SURAT_IZIN_NIKAH_FINAL' && !in_array($application->status, [MarriageApplication::STATUS_DISETUJUI, MarriageApplication::STATUS_SELESAI])) {
            return redirect()->back()->with('error', 'Surat Izin Nikah final hanya dapat digenerate setelah pengajuan disetujui akhir (DISETUJUI).');
        }

        if ($request->jenis_surat !== 'SURAT_IZIN_NIKAH_FINAL' && !in_array($application->status, $allowedStatusesForLetter)) {
            return redirect()->back()->with('error', 'Surat pengantar hanya dapat digenerate setelah pengajuan awal disetujui (PENGAJUAN_DISETUJUI).');
        }

        $documentType = \App\Models\MarriageDocumentType::where('code', $request->jenis_surat)->first();
        if (!$documentType) {
            return redirect()->back()->with('error', 'Jenis surat tidak dikenali.');
        }

        if (!$documentType->template_path) {
            return redirect()->back()->with('error', 'Template untuk surat ini belum tersedia.');
        }

        $templatePath = storage_path('app/' . $documentType->template_path);

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('info',
                'Template fisik tidak ditemukan: ' . $documentType->template_path . '. Silakan upload di menu Pengaturan Template terlebih dahulu.'
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
            abort(404, 'File surat tidak ditemukan di storage.');
        }

        $downloadName = str_replace(['/', '\\', ' '], '_', $letter->jenis_surat) . '_' . $application->personel->nama . '.docx';
        return Storage::disk('private')->download($letter->file_generated, $downloadName);
    }
}
