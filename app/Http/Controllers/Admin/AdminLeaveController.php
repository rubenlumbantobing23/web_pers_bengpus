<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveOfficialLetter;
use App\Notifications\LeaveStatusNotification;

class AdminLeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['user.personel', 'leaveType'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('personel', function ($pq) use ($search) {
                        $pq->where('nrp_nip', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        $leaveRequests = $query->paginate(15)->withQueryString();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.index', compact('leaveRequests', 'leaveTypes'));
    }

    public function show($id)
    {
        $leaveRequest = LeaveRequest::with(['user.personel', 'leaveType', 'documents', 'approvedBy'])
            ->findOrFail($id);

        return view('admin.leave.show', compact('leaveRequest'));
    }

    public function updateStatus(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'approved_days' => 'nullable|integer|min:1|max:' . $leaveRequest->working_days_count,
            'approved_notes' => 'nullable|string|max:500',
            'signing_option' => 'nullable|in:kabeng,waka_an_kabeng,wakabeng',
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika pengajuan ditolak.',
            'approved_days.min' => 'Jumlah hari yang disetujui minimal 1 hari.',
            'approved_days.max' => 'Jumlah hari yang disetujui tidak boleh melebihi hari kerja yang diajukan (' . $leaveRequest->working_days_count . ' hari).',
        ]);

        if (in_array($leaveRequest->status, ['approved', 'rejected', 'cancelled'])) {
            return redirect()->route('admin.leave.show', $id)
                ->with('error', 'Pengajuan sudah dalam status final (' . $leaveRequest->status . ') dan tidak dapat diubah lagi melalui form ini.');
        }

        $updateData = [
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ];
        if ($request->status === 'approved') {
            // Validate and limit approved_days
            $approvedDays = $request->filled('approved_days') ? (int) $request->approved_days : $leaveRequest->working_days_count;
            if ($approvedDays < 1 || $approvedDays > $leaveRequest->working_days_count) {
                return redirect()->route('admin.leave.show', $id)
                    ->with('error', "Jumlah hari disetujui tidak valid. Harus antara 1 hingga {$leaveRequest->working_days_count} hari.");
            }

            $updateData['approved_days'] = $approvedDays;
            $updateData['approved_notes'] = $request->approved_notes;
            $updateData['approved_end_date'] = \App\Helpers\LeaveCalculator::calculateEndDate($leaveRequest->start_date, $approvedDays);
        } else {
            // Reset approved fields jika ditolak/dibatalkan
            $updateData['approved_days'] = null;
            $updateData['approved_notes'] = null;
            $updateData['approved_end_date'] = null;
        }

        $shouldNotify = false;
        $savedFilePaths = [];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($id, &$leaveRequest, $updateData, $request, &$shouldNotify, &$savedFilePaths) {
                // 1. Lock the specific leave request row to prevent concurrent decisions
                $lockedLeaveRequest = LeaveRequest::where('id', $id)->lockForUpdate()->first();
                
                // 2. Re-evaluate if status is still valid after lock
                if (in_array($lockedLeaveRequest->status, ['approved', 'rejected', 'cancelled'])) {
                    throw new \Exception("STATUS_ALREADY_FINAL:{$lockedLeaveRequest->status}");
                }

                // 3. Update the request
                $lockedLeaveRequest->update($updateData);

                // 4. Generate document if approved
                if ($request->status === 'approved') {
                    $path = $this->generateSuratCuti($lockedLeaveRequest->id, $request->signing_option);
                    if ($path && is_string($path)) {
                        $savedFilePaths[] = $path;
                    }
                    \App\Helpers\ActivityLogger::log('Update Status Cuti', "Menyetujui cuti ({$lockedLeaveRequest->request_number}) dan menerbitkan surat resmi");
                } else {
                    $statusLabel = $request->status === 'rejected' ? 'ditolak' : 'dibatalkan';
                    \App\Helpers\ActivityLogger::log('Update Status Cuti', "Mengubah status cuti ({$lockedLeaveRequest->request_number}) menjadi {$statusLabel}");
                }
                
                // 5. Update reference for notification outside transaction
                $leaveRequest = $lockedLeaveRequest;
                $shouldNotify = true;
            });

            // Transaction committed. Safely send notifications.
            if ($shouldNotify && $leaveRequest->user) {
                try {
                    $leaveRequest->user->notify(new LeaveStatusNotification($leaveRequest->load('leaveType'), $request->status));
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim notifikasi cuti: ' . $e->getMessage());
                    // Notifikasi gagal tidak membatalkan surat/status yang sudah berhasil disetujui.
                }
            }

            $statusLabel = $request->status === 'approved' ? 'disetujui dan Surat Cuti resmi diterbitkan' : ($request->status === 'rejected' ? 'ditolak' : 'dibatalkan');
            return redirect()->route('admin.leave.show', $id)
                ->with('success', "Status pengajuan cuti {$leaveRequest->request_number} berhasil diperbarui menjadi {$statusLabel}.");

        } catch (\Exception $e) {
            // Transaction rolled back. Clean up orphaned files if any.
            foreach ($savedFilePaths as $path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($path);
            }

            if (str_starts_with($e->getMessage(), "STATUS_ALREADY_FINAL:")) {
                $parts = explode(":", $e->getMessage());
                return redirect()->route('admin.leave.show', $id)
                    ->with('error', 'Pengajuan telah diproses oleh admin lain dan kini berstatus final (' . $parts[1] . ').');
            }

            \Log::error('Gagal memperbarui status cuti: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.leave.show', $id)
                ->with('error', "Gagal memperbarui pengajuan: " . ($request->status === 'approved' ? "Surat resmi tidak dapat diterbitkan atau terjadi kesalahan teknis." : "Terjadi kesalahan teknis saat menyimpan data."));
        }
    }

    public function issueSuratCuti(Request $request, $id)
    {
        $savedFilePath = null;
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($id, $request, &$savedFilePath) {
                // Lock baris pengajuan untuk mencegah duplikasi (race condition)
                $lockedLeaveRequest = \App\Models\LeaveRequest::lockForUpdate()->findOrFail($id);
                $path = $this->generateSuratCuti($lockedLeaveRequest->id, $request->signing_option);
                if ($path && is_string($path)) {
                    $savedFilePath = $path;
                }
            });

            return redirect()->route('admin.leave.show', $id)
                ->with('success', 'Surat Cuti Resmi berhasil diterbitkan.');
        } catch (\Exception $e) {
            if ($savedFilePath) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($savedFilePath);
            }
            \Log::error('Gagal menerbitkan ulang surat cuti: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal menerbitkan surat resmi: Terjadi kesalahan teknis atau surat sudah pernah diterbitkan.');
        }
    }

    private function generateSuratCuti($id, $signingOption = null)
    {
        $leaveRequest = LeaveRequest::with(['user.personel', 'leaveType'])->findOrFail($id);
        
        if ($leaveRequest->status !== 'approved') {
            throw new \Exception('Surat Cuti Resmi belum dapat diterbitkan karena pengajuan belum disetujui.');
        }
        
        // Prevent duplicate generation (just in case UI bugs out)
        $existingLetter = LeaveOfficialLetter::where('leave_request_id', $id)->first();
        if ($existingLetter) {
            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($existingLetter->file_path) && !\Illuminate\Support\Facades\Storage::disk('public')->exists($existingLetter->file_path)) {
                // File hilang, hapus record yatim dan regenerasi
                $existingLetter->delete();
            } else {
                throw new \Exception('Surat Cuti sudah pernah diterbitkan. Sistem tidak menimpa surat lama secara otomatis.');
            }
        }

        $user = $leaveRequest->user;
        $personel = $user->personel;
        
        $kategori = $personel ? $personel->kategori_personel : '';
        $isPejabat = false;
        if ($personel) {
            $isPejabat = \App\Models\OrganizationOfficialAssignment::where('personel_id', $personel->id)
                ->where('is_active', true)
                ->exists();
        }

        if ($isPejabat || in_array($kategori, ['Perwira Menengah', 'Perwira Pertama'])) {
            if (!$signingOption) {
                throw new \Exception('Opsi penandatangan wajib diisi untuk perwira atau pejabat.');
            }
            $templateType = 'surat_cuti_perwira';
            $signerRole = $signingOption === 'kabeng' ? 'kabeng' : 'wakabeng';
        } elseif (in_array($kategori, ['Bintara', 'Tamtama'])) {
            $signingOption = 'wakabeng';
            $templateType = 'surat_cuti_bintara_tamtama';
            $signerRole = 'wakabeng';
        } elseif ($kategori === 'PNS') {
            $signingOption = 'wakabeng';
            $templateType = 'surat_cuti_pns';
            $signerRole = 'wakabeng';
        } else {
            $signingOption = 'wakabeng';
            $templateType = 'surat_cuti_bintara_tamtama';
            $signerRole = 'wakabeng';
        }

        $templatePath = storage_path('app/templates/template_' . $templateType . '.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('Template Surat Cuti untuk kategori ini belum dikonfigurasi. Silakan konfigurasi template terlebih dahulu.');
        }

        // Get signer info from OrganizationStructureService
        $structureService = app(\App\Services\OrganizationStructureService::class);
        $signerPersonel = $structureService->getSignerPersonel($signerRole);
        
        if (!$signerPersonel) {
            throw new \Exception('Pejabat penandatangan (' . strtoupper($signerRole) . ') belum dikonfigurasi pada menu Struktur Organisasi. Surat belum dapat diterbitkan.');
        }

        $nama_pemohon = $personel ? $personel->nama : $user->name;
        $pangkat_pemohon = $personel ? trim($personel->pangkat_golongan) : '-';
        $nrp_pemohon = $personel ? trim($personel->nrp_nip) : '-';
        $jabatan_pemohon = $personel ? $personel->jabatan : '-';
        $satuan_pemohon = $personel ? $personel->satuan_bagian : '-';
        $corps_pemohon = $personel ? trim($personel->corps ?: '-') : '-';
        
        $leaveTypeName = $leaveRequest->leaveType ? $leaveRequest->leaveType->name : 'Cuti Tahunan';
        
        // Use approved_days if available, otherwise fallback to working_days_count
        $totalDays = $leaveRequest->approved_days ?? $leaveRequest->working_days_count;
        
        $startDateStr = \Carbon\Carbon::parse($leaveRequest->start_date)->locale('id')->isoFormat('D MMMM Y');
        $endDateRaw = $leaveRequest->approved_end_date ?? $leaveRequest->end_date;
        $endDateStr = \Carbon\Carbon::parse($endDateRaw)->locale('id')->isoFormat('D MMMM Y');
        
        $tglPengajuanStr = \Carbon\Carbon::parse($leaveRequest->created_at)->locale('id')->isoFormat('D MMMM Y');
        $tglTerbitStr = now()->locale('id')->isoFormat('D MMMM Y');
        $issuedAt = now();

        $noHp = $leaveRequest->emergency_contact ?: '-';
        $alasan = $leaveRequest->reason ?: '-';

        $signerCorps = trim($structureService->resolveCorpsForPersonel($signerPersonel) ?? '');

        // Snapshot data
        $snapshot = [
            'pemohon' => [
                'nama' => $nama_pemohon,
                'pangkat' => $pangkat_pemohon,
                'nrp' => $nrp_pemohon,
                'jabatan' => $jabatan_pemohon,
                'satuan' => $satuan_pemohon,
                'corps' => $corps_pemohon,
                'kategori' => $kategori,
                'no_hp' => $noHp,
            ],
            'penandatangan' => [
                'role' => $signerRole,
                'nama' => $signerPersonel->nama,
                'pangkat' => trim($signerPersonel->pangkat_golongan),
                'corps' => $signerCorps,
                'nrp' => trim($signerPersonel->nrp_nip),
                'jabatan' => $signerPersonel->jabatan,
            ],
            'cuti' => [
                'jenis' => $leaveTypeName,
                'tgl_mulai' => $startDateStr,
                'tgl_selesai' => $endDateStr,
                'total_hari' => $totalDays,
                'alasan' => $alasan,
                'tgl_pengajuan' => $tglPengajuanStr,
                'tgl_terbit' => $tglTerbitStr,
            ]
        ];

        // Process DOCX
        $templateProcessor = new class($templatePath) extends \PhpOffice\PhpWord\TemplateProcessor {
            public function __construct($documentTemplate) {
                parent::__construct($documentTemplate);
                // Fix MS Word collapsing trailing spaces by forcing space preservation on text runs
                $this->tempDocumentMainPart = str_replace('<w:t>', '<w:t xml:space="preserve">', $this->tempDocumentMainPart);
                foreach ($this->tempDocumentHeaders as $index => $xml) {
                    $this->tempDocumentHeaders[$index] = str_replace('<w:t>', '<w:t xml:space="preserve">', $xml);
                }
                foreach ($this->tempDocumentFooters as $index => $xml) {
                    $this->tempDocumentFooters[$index] = str_replace('<w:t>', '<w:t xml:space="preserve">', $xml);
                }
            }
        };
        
        $templateProcessor->setValue('tgl_pengajuan', $tglPengajuanStr);
        $templateProcessor->setValue('tgl_terbit', $tglTerbitStr);
        
        $templateProcessor->setValue('nama', $nama_pemohon);
        $templateProcessor->setValue('pangkat', $pangkat_pemohon);
        $templateProcessor->setValue('nrp', $nrp_pemohon);
        $templateProcessor->setValue('jabatan', $jabatan_pemohon);
        
        // Corps di blok tanda tangan diambil dari nominatif penandatangan (via NRP/personel)
        $templateProcessor->setValue('corps', $signerCorps);
        $templateProcessor->setValue('corps_kabeng', $signerCorps);
        $templateProcessor->setValue('corps_waka', $signerCorps);
        $templateProcessor->setValue('corps_penandatangan', $signerCorps);
        $templateProcessor->setValue('corps_pemohon', $corps_pemohon);
        
        $templateProcessor->setValue('tujuan', $leaveRequest->tujuan ?? '-');
        $templateProcessor->setValue('kendaraan', $leaveRequest->kendaraan ?? '-');
        $templateProcessor->setValue('pengikut', $leaveRequest->pengikut ?? '-');
        $templateProcessor->setValue('jenis_cuti', $leaveTypeName);
        $templateProcessor->setValue('tgl_mulai', $startDateStr);
        $templateProcessor->setValue('tgl_selesai', $endDateStr);
        $templateProcessor->setValue('total_hari', $totalDays);
        $templateProcessor->setValue('kodim', $leaveRequest->kodim_koramil ?? '-');

        // Generic penandatangan placeholders
        $templateProcessor->setValue('nama_penandatangan', $snapshot['penandatangan']['nama']);
        $templateProcessor->setValue('pangkat_penandatangan', $snapshot['penandatangan']['pangkat']);
        $templateProcessor->setValue('nrp_penandatangan', $snapshot['penandatangan']['nrp']);

        // Dynamic Role mapping based on the template logic
        if ($templateType === 'surat_cuti_perwira') {
            // surat_cuti_perwira uses kabeng placeholders directly
            $templateProcessor->setValue('nama_kabeng', $snapshot['penandatangan']['nama']);
            $templateProcessor->setValue('pangkat_kabeng', $snapshot['penandatangan']['pangkat']);
            $templateProcessor->setValue('nrp_kabeng', $snapshot['penandatangan']['nrp']);
        } else {
            // bintara, tamtama, pns uses waka placeholders
            $templateProcessor->setValue('nama_waka', $snapshot['penandatangan']['nama']);
            $templateProcessor->setValue('pangkat_waka', $snapshot['penandatangan']['pangkat']);
            $templateProcessor->setValue('nrp_waka', $snapshot['penandatangan']['nrp']);
        }

        $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '', $nama_pemohon);
        $fileName = \Illuminate\Support\Str::uuid() . '_Surat_Cuti_' . ($cleanName ?: 'Anggota') . '.docx';
        $savePath = 'official_letters/' . $fileName;
        
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
        $templateProcessor->saveAs($tempFile);
        
        $saved = Storage::disk('local')->put($savePath, file_get_contents($tempFile));
        @unlink($tempFile);

        if (!$saved) {
            throw new \Exception('Gagal menyimpan file surat cuti ke server.');
        }

        try {
            LeaveOfficialLetter::create([
                'leave_request_id' => $leaveRequest->id,
                'template_used' => $templateType,
                'signing_option' => $signingOption,
                'snapshot_data' => $snapshot,
                'file_path' => $savePath,
                'issued_at' => $issuedAt,
            ]);

            \App\Helpers\ActivityLogger::log('Terbit Surat Cuti', "Menerbitkan Surat Cuti Resmi untuk {$nama_pemohon}");
        } catch (\Exception $e) {
            // Hapus file orphan jika penyimpanan record ke DB gagal atau logging gagal
            \Illuminate\Support\Facades\Storage::disk('local')->delete($savePath);
            throw $e;
        }

        return $savePath;
    }

    public function downloadSuratCuti($id)
    {
        $letter = LeaveOfficialLetter::where('leave_request_id', $id)->first();
        if (!$letter) {
            abort(404, 'Surat Cuti Resmi belum diterbitkan.');
        }

        if (!Storage::disk('local')->exists($letter->file_path) && !Storage::disk('public')->exists($letter->file_path)) {
            abort(404, 'File Surat Cuti Resmi tidak ditemukan di server.');
        }

        $leaveRequest = $letter->leaveRequest;
        $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '', $leaveRequest->user->personel->nama ?? $leaveRequest->user->name);
        $downloadName = "Surat_Cuti_" . ($cleanName ?: 'Anggota') . ".docx";

        if (!Storage::disk('local')->exists($letter->file_path) && Storage::disk('public')->exists($letter->file_path)) {
            return Storage::disk('public')->download($letter->file_path, $downloadName);
        }

        return Storage::disk('local')->download($letter->file_path, $downloadName);
    }

    public function downloadDocument($id, $docId)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $document = \App\Models\LeaveRequestDocument::where('leave_request_id', $id)->findOrFail($docId);

        if (!Storage::disk('local')->exists($document->file_path) && !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File lampiran tidak ditemukan di server.');
        }

        if (!Storage::disk('local')->exists($document->file_path) && Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path);
        }

        return Storage::disk('local')->download($document->file_path);
    }
}
