<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestDocument;
use App\Models\Holiday;
use App\Helpers\LeaveCalculator;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->with(['leaveType', 'documents'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $entitlement = LeaveCalculator::getUserEntitlementSummary($user->id);

        return view('user.leave.index', compact('leaveRequests', 'entitlement'));
    }

    public function create(Request $request)
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $selectedLeaveTypeId = $request->query('leave_type_id');
        $selectedLeaveType = $selectedLeaveTypeId ? LeaveType::find($selectedLeaveTypeId) : null;

        $entitlement = LeaveCalculator::getUserEntitlementSummary(Auth::id());

        // Get national holidays for frontend calendar
        $holidays = Holiday::whereYear('date', date('Y'))
            ->get()
            ->map(function ($h) {
                return [
                    'date' => is_string($h->date) ? substr($h->date, 0, 10) : $h->date->format('Y-m-d'),
                    'name' => $h->name,
                    'type' => $h->type,
                ];
            });

        // Get existing leave requests of user to highlight on calendar
        $existingLeaves = LeaveRequest::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->get(['start_date', 'end_date', 'approved_end_date', 'status'])
            ->map(function ($l) {
                $effectiveEnd = ($l->status === 'approved' && $l->approved_end_date) ? $l->approved_end_date : $l->end_date;
                return [
                    'start' => is_string($l->start_date) ? substr($l->start_date, 0, 10) : $l->start_date->format('Y-m-d'),
                    'end' => is_string($effectiveEnd) ? substr($effectiveEnd, 0, 10) : $effectiveEnd->format('Y-m-d'),
                    'status' => $l->status,
                ];
            });

        $user = Auth::user();
        $personel = $user->personel;
        $userGender = $personel ? $personel->jenis_kelamin : null;

        return view('user.leave.create', compact(
            'leaveTypes',
            'selectedLeaveType',
            'entitlement',
            'holidays',
            'existingLeaves',
            'userGender'
        ));
    }

    public function downloadPermohonanTemplate(Request $request)
    {
        try {
            $user = Auth::user();
            $personel = $user->personel;

            $nama = $personel ? $personel->nama : $user->name;
            $pangkat = $personel ? $personel->pangkat_golongan : '-';
            $nrp = $personel ? $personel->nrp_nip : '-';
            $jabatan = $personel ? str_replace(["\r", "\n"], ' ', $personel->jabatan) : '-';
            $satuan = $personel ? $personel->satuan_bagian : 'Bengpuskomlekad';
            $kategori = $personel ? $personel->kategori_personel : '';
            $leaveTypeName = 'Cuti Tahunan';
            if ($request->filled('leave_type_id')) {
                $lt = LeaveType::find($request->leave_type_id);
                if ($lt) {
                    $leaveTypeName = $lt->name;
                }
            }

            $startDateRaw = $request->input('start_date');
            $endDateRaw = $request->input('end_date');
            
            $startDate = $startDateRaw ? \Carbon\Carbon::parse($startDateRaw)->locale('id')->isoFormat('D MMMM Y') : '..............';
            $endDate = $endDateRaw ? \Carbon\Carbon::parse($endDateRaw)->locale('id')->isoFormat('D MMMM Y') : '..............';

            $corps = $personel ? ($personel->corps ?: '-') : '-';

            $isPejabat = false;
            if ($personel) {
                $isPejabat = \App\Models\OrganizationOfficialAssignment::where('personel_id', $personel->id)
                    ->where('is_active', true)
                    ->exists();
            }

            $templateType = 'permohonan_bintara_tamtama';
            if ($isPejabat) {
                $templateType = 'permohonan_pejabat';
            } elseif (in_array($kategori, ['Perwira Menengah', 'Perwira Pertama'])) {
                $templateType = 'permohonan_perwira';
            } elseif (in_array($kategori, ['Bintara', 'Tamtama'])) {
                $templateType = 'permohonan_bintara_tamtama';
            } elseif ($kategori === 'PNS') {
                $templateType = 'permohonan_pns';
            }
            $templatePath = storage_path('app/templates/template_' . $templateType . '.docx');
            
            if (!file_exists($templatePath)) {
                return response()->json([
                    'error' => 'Template Surat Permohonan untuk kategori ini belum dikonfigurasi oleh Admin. Hubungi Staf Personalia.'
                ], 404);
            }

            // Resolve signers and supervisor from Organization Structure Service
            $structureService = app(\App\Services\OrganizationStructureService::class);
            $supervisor = $structureService->getSupervisorForPersonel($personel);

            $templateProcessor = $this->createTemplateProcessorWithSeparatedCorps($templatePath);
            
            $templateProcessor->setValue('tgl_pengajuan', now()->locale('id')->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('nama', $nama);
            $templateProcessor->setValue('pangkat', $pangkat);
            $templateProcessor->setValue('nrp', $nrp);
            $templateProcessor->setValue('jabatan', $jabatan);
            $templateProcessor->setValue('satuan', $satuan);
            // Applicant corps (for pemohon block)
            $corpsStr = $corps ? trim($corps) : '';
            $templateProcessor->setValue('corps', $corpsStr);
            $templateProcessor->setValue('corps_pemohon', $corpsStr);
            $templateProcessor->setValue('no_hp', $request->input('emergency_contact') ?? '-');
            $templateProcessor->setValue('alasan', $request->input('reason') ?? '-');
            $templateProcessor->setValue('jenis_cuti', $leaveTypeName);
            $templateProcessor->setValue('tgl_mulai', $startDate);
            $templateProcessor->setValue('tgl_selesai', $endDate);

            // Map signers (ambil corps penandatangan dari nominatif personel via NRP)
            // 1. Atasan Langsung (Kasub / Kabag)
            $corpsKabag = $structureService->resolveCorpsForPersonel($supervisor);
            $corpsKabagStr = $corpsKabag ? trim($corpsKabag) : '';
            $templateProcessor->setValue('nama_kabag', $supervisor ? $supervisor->nama : '-');
            $templateProcessor->setValue('pangkat_kabag', $supervisor ? trim($supervisor->pangkat_golongan) : '-');
            $templateProcessor->setValue('corps_kabag', $corpsKabagStr);
            $templateProcessor->setValue('nrp_kabag', $supervisor ? $supervisor->nrp_nip : '-');

            $roles = ['kabagum', 'waka', 'kabeng'];
            foreach ($roles as $role) {
                $p = $structureService->getSignerPersonel($role);

                $corpsRole = $structureService->resolveCorpsForPersonel($p);
                $corpsRoleStr = $corpsRole ? trim($corpsRole) : '';
                $templateProcessor->setValue('nama_' . $role, $p ? $p->nama : '-');
                $pangkatRole = $p ? trim($p->pangkat_golongan) : '-';
                $templateProcessor->setValue('pangkat_' . $role, $pangkatRole);
                $templateProcessor->setValue('corps_' . $role, $corpsRoleStr);
                $templateProcessor->setValue('nrp_' . $role, $p ? $p->nrp_nip : '-');
            }
            
            // Count total days
            try {
                $totalDays = $startDateRaw && $endDateRaw ? \Carbon\Carbon::parse($startDateRaw)->diffInDays(\Carbon\Carbon::parse($endDateRaw)) + 1 : '-';
            } catch (\Exception $e) {
                $totalDays = '-';
            }
            $templateProcessor->setValue('total_hari', $totalDays);

            $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '', $nama);
            $fileName = 'Surat_Permohonan_Cuti_' . ($cleanName ?: 'Anggota') . '.docx';

            // Gunakan temp file dengan ekstensi .docx eksplisit
            // (tempnam() tanpa ekstensi bisa menyebabkan nama file UUID saat download di Windows)
            $tempDir = sys_get_temp_dir();
            $tempFile = $tempDir . DIRECTORY_SEPARATOR . 'phpword_' . uniqid() . '.docx';
            $templateProcessor->saveAs($tempFile);

            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Download Permohonan Template Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('<html><body style="font-family:sans-serif;padding:40px"><h2 style="color:red">Gagal Generate Template</h2><p>' . e($e->getMessage()) . '</p><a href="javascript:history.back()">Kembali</a></body></html>', 500)
                ->header('Content-Type', 'text/html');
        }
    }



    public function calculateDays(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $workingDays = LeaveCalculator::calculateWorkingDays($request->start_date, $request->end_date);

        return response()->json([
            'working_days' => $workingDays,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        if (!$leaveType->is_active) {
            return back()->withInput()->withErrors([
                'leave_type_id' => 'Jenis cuti yang Anda pilih saat ini sedang tidak aktif.',
            ]);
        }

        $rules = [
            'leave_type_id' => 'required|exists:leave_types,id',
            'terms_accepted' => 'required|accepted',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'tujuan' => 'required|string|max:255',
            'pengikut' => 'required|string|max:255',
            'kendaraan' => 'required|string|max:255',
            'kodim_koramil' => 'required|string|max:255',
            'emergency_contact' => 'required|string|max:100',
            'permohonan_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        if (!empty($leaveType->required_documents_info) && $leaveType->code !== 'CT_TAHUNAN') {
            $rules['supporting_documents'] = 'required|array|min:1';
            $rules['supporting_documents.*'] = 'file|mimes:pdf,jpg,jpeg,png|max:5120';
        } else {
            $rules['supporting_documents'] = 'nullable|array';
            $rules['supporting_documents.*'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $messages = [
            'terms_accepted.accepted' => 'Anda harus membaca dan menyetujui syarat dan ketentuan sebelum mengajukan cuti.',
            'start_date.after_or_equal' => 'Tanggal mulai cuti tidak boleh di masa lalu.',
            'end_date.after_or_equal' => 'Tanggal selesai cuti harus sama atau setelah tanggal mulai.',
            'supporting_documents.required' => 'Jenis cuti ini mewajibkan Anda untuk mengunggah dokumen pendukung tambahan.',
        ];

        $request->validate($rules, $messages);

        $user = Auth::user();
        $workingDays = LeaveCalculator::calculateWorkingDays($request->start_date, $request->end_date);

        if ($workingDays <= 0) {
            return back()->withInput()->withErrors([
                'start_date' => 'Periode yang Anda pilih tidak memiliki hari kerja (seluruhnya hari libur / akhir pekan).',
            ]);
        }



        $uploadedFiles = [];
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $user, $leaveType, $workingDays, &$uploadedFiles) {
                // Lock the user row to serialize all leave request creations for this user.
                // This prevents race conditions even when no overlapping rows exist yet.
                \App\Models\User::where('id', $user->id)->lockForUpdate()->first();

                // Validasi ulang is_active dari record terbaru di dalam lock
                $lockedLeaveType = \App\Models\LeaveType::where('id', $leaveType->id)->lockForUpdate()->first();
                if (!$lockedLeaveType || !$lockedLeaveType->is_active) {
                    throw new \Exception("INACTIVE");
                }

                $entitlement = LeaveCalculator::getUserEntitlementSummary($user->id);

                // Validasi jatah cuti khusus Cuti Tahunan, Cuti Kawin, dan jenis cuti lainnya
                if ($lockedLeaveType->code === 'CT_TAHUNAN') {
                    if ($workingDays > $entitlement['available_after_pending']) {
                        throw new \Exception("QUOTA_EXCEEDED:{$entitlement['available_after_pending']}");
                    }
                } elseif ($lockedLeaveType->code === 'CT_KAWIN') {
                    $personel = $user->personel;
                    $gender = $personel ? $personel->jenis_kelamin : null;

                    if (empty($gender)) {
                        throw new \Exception("GENDER_REQUIRED");
                    }

                    $genderLower = strtolower($gender);
                    $isFemale = str_contains($genderLower, 'wanita') || str_contains($genderLower, 'perempuan') || $genderLower === 'p' || $genderLower === 'w';
                    $maxDays = $isFemale ? 6 : 3;
                    $genderLabel = $isFemale ? 'Wanita' : 'Pria';

                    if ($workingDays > $maxDays) {
                        throw new \Exception("KAWIN_EXCEEDED:{$maxDays}:{$genderLabel}");
                    }
                } elseif ($lockedLeaveType->default_days > 0 && $workingDays > $lockedLeaveType->default_days) {
                    throw new \Exception("DEFAULT_EXCEEDED:{$lockedLeaveType->default_days}");
                }

                // Check overlapping leave requests
                $overlap = LeaveRequest::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                            ->orWhereBetween(\Illuminate\Support\Facades\DB::raw('COALESCE(approved_end_date, end_date)'), [$request->start_date, $request->end_date])
                            ->orWhere(function ($q) use ($request) {
                                $q->where('start_date', '<=', $request->start_date)
                                    ->where(\Illuminate\Support\Facades\DB::raw('COALESCE(approved_end_date, end_date)'), '>=', $request->end_date);
                            });
                    })->exists();

                if ($overlap) {
                    throw new \Exception("OVERLAP");
                }

                $requestNumber = 'CUTI/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -5));

                $leaveRequest = LeaveRequest::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $lockedLeaveType->id,
                    'request_number' => $requestNumber,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'working_days_count' => $workingDays,
                    'reason' => $request->reason,
                    'tujuan' => $request->tujuan,
                    'pengikut' => $request->pengikut,
                    'kendaraan' => $request->kendaraan,
                    'kodim_koramil' => $request->kodim_koramil,
                    'emergency_contact' => $request->emergency_contact,
                    'status' => 'pending',
                ]);

                    // Save Surat Permohonan
                    if ($request->hasFile('permohonan_document')) {
                        $file = $request->file('permohonan_document');
                        $filename = \Illuminate\Support\Str::uuid() . '.' . $file->extension();
                        $path = $file->storeAs('leave_documents', $filename, 'local');
                        if (!$path) {
                            throw new \Exception("UPLOAD_FAILED");
                        }
                        $uploadedFiles[] = $path;
                        LeaveRequestDocument::create([
                            'leave_request_id' => $leaveRequest->id,
                            'document_name' => '[Surat Permohonan] ' . $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_size' => $file->getSize(),
                        ]);
                    }

                    // Save Supporting Documents
                    if ($request->hasFile('supporting_documents')) {
                        foreach ($request->file('supporting_documents') as $file) {
                            $filename = \Illuminate\Support\Str::uuid() . '.' . $file->extension();
                            $path = $file->storeAs('leave_documents', $filename, 'local');
                            if (!$path) {
                                throw new \Exception("UPLOAD_FAILED");
                            }
                            $uploadedFiles[] = $path;
                            LeaveRequestDocument::create([
                                'leave_request_id' => $leaveRequest->id,
                                'document_name' => '[Lampiran Pendukung] ' . $file->getClientOriginalName(),
                                'file_path' => $path,
                                'file_size' => $file->getSize(),
                            ]);
                        }
                    }

                    \App\Helpers\ActivityLogger::log('Pengajuan Cuti', "Mengajukan cuti baru ({$lockedLeaveType->name}) mulai " . \Carbon\Carbon::parse($request->start_date)->format('d M Y'));

                    $admins = \App\Models\User::where('role', 'admin')->get();
                    $notification = new \App\Notifications\NewApplicationNotification(
                        'Cuti',
                        $user->name,
                        route('admin.leave.show', $leaveRequest->id)
                    );
                    foreach ($admins as $admin) {
                        $admin->notify($notification);
                    }

                return redirect()->route('user.leave.show', $leaveRequest->id)
                    ->with('success', 'Pengajuan cuti berhasil dikirim! Menunggu verifikasi dari Staf Personalia.');
            });
        } catch (\Exception $e) {
            // Cleanup files if the transaction fails (even during commit)
            foreach ($uploadedFiles as $path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($path);
            }

            $msg = $e->getMessage();
            if ($msg === "INACTIVE") {
                return back()->withInput()->withErrors(['leave_type_id' => 'Jenis cuti yang Anda pilih saat ini sedang tidak aktif.']);
            } elseif (str_starts_with($msg, "QUOTA_EXCEEDED:")) {
                $quota = explode(':', $msg)[1];
                return back()->withInput()->withErrors(['start_date' => "Jumlah hari kerja yang diajukan ({$workingDays} hari) melebihi sisa jatah cuti tersedia Anda ({$quota} hari)."]);
            } elseif ($msg === "GENDER_REQUIRED") {
                return back()->withInput()->withErrors(['start_date' => "Silakan lengkapi jenis kelamin Anda terlebih dahulu pada menu Profil Saya sebelum mengajukan Cuti Kawin."]);
            } elseif (str_starts_with($msg, "KAWIN_EXCEEDED:")) {
                $parts = explode(':', $msg);
                return back()->withInput()->withErrors(['start_date' => "Jumlah hari kerja yang diajukan ({$workingDays} hari) melebihi batas maksimal Cuti Kawin untuk prajurit/anggota {$parts[2]} (maksimal {$parts[1]} hari kerja). Pelaksanaan cuti menyesuaikan kebijakan atasan."]);
            } elseif (str_starts_with($msg, "DEFAULT_EXCEEDED:")) {
                $limit = explode(':', $msg)[1];
                return back()->withInput()->withErrors(['start_date' => "Jumlah hari kerja yang diajukan ({$workingDays} hari) melebihi batas maksimal untuk {$leaveType->name} ({$limit} hari)."]);
            } elseif ($msg === "UPLOAD_FAILED") {
                return back()->withInput()->with('error', 'Gagal menyimpan dokumen. Silakan periksa ukuran dan jenis file, lalu coba lagi.');
            } elseif ($msg === "OVERLAP") {
                return back()->withInput()->withErrors([
                    'start_date' => 'Anda sudah memiliki pengajuan cuti lain pada rentang tanggal tersebut.',
                ]);
            }
            return back()->withInput()->with('error', 'Terjadi kesalahan teknis saat menyimpan pengajuan, silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $leaveRequest = LeaveRequest::where('user_id', Auth::id())
            ->with(['leaveType', 'documents', 'approvedBy'])
            ->findOrFail($id);

        return view('user.leave.show', compact('leaveRequest'));
    }

    public function cancel($id)
    {
        $leaveRequest = LeaveRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $leaveRequest->update([
            'status' => 'cancelled',
        ]);

        \App\Helpers\ActivityLogger::log('Pembatalan Cuti', "Membatalkan pengajuan cuti ({$leaveRequest->request_number})");

        return redirect()->route('user.leave.index')
            ->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }

    public function downloadSuratCuti($id)
    {
        $leaveRequest = LeaveRequest::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($leaveRequest->status !== 'approved') {
            abort(403, 'Surat Cuti hanya dapat diunduh jika pengajuan telah disetujui.');
        }

        $letter = \App\Models\LeaveOfficialLetter::where('leave_request_id', $id)->first();
        if (!$letter) {
            abort(404, 'Surat Cuti Resmi belum diterbitkan oleh Admin.');
        }

        if (!Storage::disk('local')->exists($letter->file_path) && !Storage::disk('public')->exists($letter->file_path)) {
            abort(404, 'File Surat Cuti Resmi tidak ditemukan di server.');
        }

        $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '', $leaveRequest->user->personel->nama ?? $leaveRequest->user->name);
        $downloadName = "Surat_Cuti_" . ($cleanName ?: 'Anggota') . ".docx";

        // Fallback for existing public files
        if (!Storage::disk('local')->exists($letter->file_path) && Storage::disk('public')->exists($letter->file_path)) {
            return Storage::disk('public')->download($letter->file_path, $downloadName);
        }

        return Storage::disk('local')->download($letter->file_path, $downloadName);
    }

    /**
     * Create TemplateProcessor while ensuring signature corps placeholders
     * are separated from applicant corps placeholder (${corps_kabag}, etc.).
     */
    protected function createTemplateProcessorWithSeparatedCorps(string $templatePath): \PhpOffice\PhpWord\TemplateProcessor
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'tpl_') . '.docx';
        copy($templatePath, $tempPath);

        $zip = new \ZipArchive();
        if ($zip->open($tempPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml !== false) {
                $newXml = preg_replace_callback('/<w:p\b[^>]*>(?:(?!<\/w:p>).)*?<\/w:p>/s', function ($m) {
                    $pXml = $m[0];
                    $text = strip_tags($pXml);

                    if (strpos($text, '${corps}') === false && strpos($pXml, '${corps}') === false) {
                        return $pXml;
                    }

                    $role = null;
                    if (strpos($text, 'kabagum') !== false) {
                        $role = 'kabagum';
                    } elseif (strpos($text, 'kabag') !== false) {
                        $role = 'kabag';
                    } elseif (strpos($text, 'waka') !== false) {
                        $role = 'waka';
                    } elseif (strpos($text, 'kabeng') !== false) {
                        $role = 'kabeng';
                    } elseif (strpos($text, 'penandatangan') !== false) {
                        $role = 'penandatangan';
                    }

                    if ($role) {
                        return str_replace('${corps}', '${corps_' . $role . '}', $pXml);
                    }

                    return $pXml;
                }, $xml);

                if ($newXml !== $xml) {
                    $zip->addFromString('word/document.xml', $newXml);
                }
            }
            $zip->close();
        }

        $processor = new class($tempPath) extends \PhpOffice\PhpWord\TemplateProcessor {
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
        @unlink($tempPath);
        return $processor;
    }

    public function downloadDocument($id, $docId)
    {
        $leaveRequest = LeaveRequest::where('user_id', Auth::id())->findOrFail($id);
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
