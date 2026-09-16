<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveOfficialLetter;

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
        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'approved_days' => 'nullable|integer|min:1',
            'approved_notes' => 'nullable|string|max:500',
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika pengajuan ditolak.',
            'approved_days.min' => 'Jumlah hari yang disetujui minimal 1 hari.',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $updateData = [
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ];

        if ($request->status === 'approved') {
            // Default ke working_days_count jika admin tidak mengisi approved_days
            $updateData['approved_days'] = $request->filled('approved_days')
                ? (int) $request->approved_days
                : $leaveRequest->working_days_count;
            $updateData['approved_notes'] = $request->approved_notes;
        } else {
            // Reset approved_days jika ditolak/dibatalkan
            $updateData['approved_days'] = null;
            $updateData['approved_notes'] = null;
        }

        $leaveRequest->update($updateData);

        $statusLabel = $request->status === 'approved'
            ? 'disetujui'
            : ($request->status === 'rejected' ? 'ditolak' : 'dibatalkan');

        \App\Helpers\ActivityLogger::log('Update Status Cuti', "Mengubah status cuti ({$leaveRequest->request_number}) menjadi {$statusLabel}");

        return redirect()->route('admin.leave.show', $id)
            ->with('success', "Status pengajuan cuti {$leaveRequest->request_number} berhasil diperbarui menjadi {$statusLabel}.");
    }

    public function issueSuratCuti(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with(['user.personel', 'leaveType'])->findOrFail($id);
        
        if ($leaveRequest->status !== 'approved') {
            return back()->with('error', 'Surat Cuti Resmi belum dapat diterbitkan karena pengajuan belum disetujui.');
        }
        
        // Prevent duplicate generation (just in case UI bugs out)
        $existingLetter = LeaveOfficialLetter::where('leave_request_id', $id)->first();
        if ($existingLetter) {
            return back()->with('error', 'Surat Cuti Resmi sudah diterbitkan sebelumnya.');
        }

        $user = $leaveRequest->user;
        $personel = $user->personel;
        
        $kategori = $personel ? $personel->kategori_personel : '';
        if (in_array($kategori, ['Perwira Menengah', 'Perwira Pertama'])) {
            $request->validate([
                'signing_option' => 'required|in:kabeng,waka_an_kabeng'
            ]);
            $signingOption = $request->signing_option;
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
            return back()->with('error', 'Template Surat Cuti untuk kategori ini belum dikonfigurasi. Silakan konfigurasi template terlebih dahulu.');
        }

        // Get signer info from OrganizationStructureService
        $structureService = app(\App\Services\OrganizationStructureService::class);
        $signerPersonel = $structureService->getSignerPersonel($signerRole);
        
        if (!$signerPersonel) {
            return back()->with('error', 'Pejabat penandatangan (' . strtoupper($signerRole) . ') belum dikonfigurasi pada menu Struktur Organisasi. Surat belum dapat diterbitkan.');
        }

        $nama_pemohon = $personel ? $personel->nama : $user->name;
        $pangkat_pemohon = $personel ? trim($personel->pangkat_golongan) : '-';
        $nrp_pemohon = $personel ? trim($personel->nrp_nip) : '-';
        $jabatan_pemohon = $personel ? $personel->jabatan : '-';
        $satuan_pemohon = $personel ? $personel->satuan_bagian : '-';
        $corps_pemohon = $personel ? trim($personel->corps ?: '-') : '-';
        
        $leaveTypeName = $leaveRequest->leaveType ? $leaveRequest->leaveType->name : 'Cuti Tahunan';
        $totalDays = \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1;
        $startDateStr = \Carbon\Carbon::parse($leaveRequest->start_date)->locale('id')->isoFormat('D MMMM Y');
        $endDateStr = \Carbon\Carbon::parse($leaveRequest->end_date)->locale('id')->isoFormat('D MMMM Y');
        
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
        $fileName = time() . '_Surat_Cuti_' . ($cleanName ?: 'Anggota') . '.docx';
        $savePath = 'official_letters/' . $fileName;
        
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
        $templateProcessor->saveAs($tempFile);
        
        Storage::disk('public')->put($savePath, file_get_contents($tempFile));
        unlink($tempFile);

        LeaveOfficialLetter::create([
            'leave_request_id' => $leaveRequest->id,
            'template_used' => $templateType,
            'signing_option' => $signingOption,
            'snapshot_data' => $snapshot,
            'file_path' => $savePath,
            'issued_at' => $issuedAt,
        ]);

        \App\Helpers\ActivityLogger::log('Terbit Surat Cuti', "Menerbitkan Surat Cuti Resmi untuk {$nama_pemohon}");

        return redirect()->route('admin.leave.show', $id)
            ->with('success', 'Surat Cuti Resmi berhasil diterbitkan.');
    }

    public function downloadSuratCuti($id)
    {
        $letter = LeaveOfficialLetter::where('leave_request_id', $id)->first();
        if (!$letter) {
            abort(404, 'Surat Cuti Resmi belum diterbitkan.');
        }

        if (!Storage::disk('public')->exists($letter->file_path)) {
            abort(404, 'File Surat Cuti Resmi tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($letter->file_path);
    }
}
