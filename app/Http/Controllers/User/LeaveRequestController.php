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
            ->get(['start_date', 'end_date', 'status'])
            ->map(function ($l) {
                return [
                    'start' => is_string($l->start_date) ? substr($l->start_date, 0, 10) : $l->start_date->format('Y-m-d'),
                    'end' => is_string($l->end_date) ? substr($l->end_date, 0, 10) : $l->end_date->format('Y-m-d'),
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

    public function downloadIzinTemplate(Request $request)
    {
        $user = Auth::user();
        $personel = $user->personel;

        $nama = $personel ? $personel->nama : $user->name;
        $pangkat = $personel ? $personel->pangkat_golongan : '-';
        $nrp = $personel ? $personel->nrp_nip : '-';
        $jabatan = $personel ? str_replace(["\r", "\n"], ' ', $personel->jabatan) : '-';
        $satuan = $personel ? $personel->satuan_bagian : 'Bengpuskomlekad';

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
        $reason = urldecode($request->input('reason', '..........................................................'));

        $structureService = app(\App\Services\OrganizationStructureService::class);
        $kabeng = $structureService->getSignerPersonel('kabeng');
        $kabengNama = $kabeng ? $kabeng->nama : '....................................................';
        $kabengPangkat = $kabeng ? $kabeng->pangkat_golongan : 'Kolonel';
        $kabengCorps = $kabeng ? ($structureService->resolveCorpsForPersonel($kabeng) ?: 'Cke') : 'Cke';
        $kabengNrp = $kabeng ? $kabeng->nrp_nip : '............................';

        $html = '
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <title>Surat Izin Cuti - Kepala Bengpuskomlekad</title>
            <style>
                body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; margin: 30px; }
                .header-kop { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; margin-bottom: 5px; }
                .line { border-bottom: 3px double #000; margin-bottom: 20px; }
                .title { text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline; margin-bottom: 5px; }
                .sub-title { text-align: center; font-size: 11pt; margin-bottom: 25px; }
                .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
                .table-data td { padding: 5px 8px; vertical-align: top; }
                .ttd-table { width: 100%; margin-top: 40px; }
                .ttd-table td { text-align: center; vertical-align: top; }
            </style>
        </head>
        <body>
            <div class="header-kop">
                BENGPUSKOMLEKAD<br>
                STAF PERSONALIA
            </div>
            <div class="line"></div>

            <div class="title">SURAT IZIN CUTI</div>
            <div class="sub-title">Nomor: SIC / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / ' . date('m') . ' / ' . date('Y') . '</div>

            <p>Diberikan izin kepada:</p>

            <table class="table-data">
                <tr>
                    <td width="30%">Nama Lengkap</td>
                    <td width="3%">:</td>
                    <td><strong>' . htmlspecialchars($nama) . '</strong></td>
                </tr>
                <tr>
                    <td>Pangkat / Golongan</td>
                    <td>:</td>
                    <td>' . htmlspecialchars($pangkat) . '</td>
                </tr>
                <tr>
                    <td>NRP / NIP</td>
                    <td>:</td>
                    <td>' . htmlspecialchars($nrp) . '</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>' . htmlspecialchars($jabatan) . '</td>
                </tr>
                <tr>
                    <td>Satuan / Bagian</td>
                    <td>:</td>
                    <td>' . htmlspecialchars($satuan) . '</td>
                </tr>
            </table>

            <p>Untuk melaksanakan <strong>' . htmlspecialchars($leaveTypeName) . '</strong> terhitung mulai tanggal <strong>' . htmlspecialchars($startDate) . '</strong> sampai dengan <strong>' . htmlspecialchars($endDate) . '</strong> dengan keperluan: <i>' . htmlspecialchars($reason) . '</i>.</p>

            <p>Demikian Surat Izin Cuti ini dikeluarkan untuk dipergunakan sebagaimana mestinya.</p>

            <table class="ttd-table">
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%;">
                        Dikeluarkan di: Jakarta<br>
                        Pada tanggal: ' . date('d F Y') . '<br><br>
                        <strong>Kepala Bengpuskomlekad</strong>
                        <br><br><br><br><br>
                        ( ' . htmlspecialchars($kabengNama) . ' )<br>
                        ' . htmlspecialchars($kabengPangkat) . ' ' . htmlspecialchars($kabengCorps) . ' NRP ' . htmlspecialchars($kabengNrp) . '
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '', $nama);
        $fileName = 'Surat_Izin_Cuti_Kabengpus_' . ($cleanName ?: 'Anggota') . '.doc';

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
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

        $entitlement = LeaveCalculator::getUserEntitlementSummary($user->id);

        // Validasi jatah cuti khusus Cuti Tahunan, Cuti Kawin, dan jenis cuti lainnya
        if ($leaveType->code === 'CT_TAHUNAN') {
            if ($workingDays > $entitlement['available_after_pending']) {
                return back()->withInput()->withErrors([
                    'start_date' => "Jumlah hari kerja yang diajukan ({$workingDays} hari) melebihi sisa jatah cuti tersedia Anda ({$entitlement['available_after_pending']} hari).",
                ]);
            }
        } elseif ($leaveType->code === 'CT_KAWIN') {
            $personel = $user->personel;
            $gender = $personel ? $personel->jenis_kelamin : null;

            if (empty($gender)) {
                return back()->withInput()->withErrors([
                    'start_date' => "Silakan lengkapi jenis kelamin Anda terlebih dahulu pada menu Profil Saya sebelum mengajukan Cuti Kawin.",
                ]);
            }

            $genderLower = strtolower($gender);
            $isFemale = str_contains($genderLower, 'wanita') || str_contains($genderLower, 'perempuan') || $genderLower === 'p' || $genderLower === 'w';
            $maxDays = $isFemale ? 6 : 3;
            $genderLabel = $isFemale ? 'Wanita' : 'Pria';

            if ($workingDays > $maxDays) {
                return back()->withInput()->withErrors([
                    'start_date' => "Jumlah hari kerja yang diajukan ({$workingDays} hari) melebihi batas maksimal Cuti Kawin untuk prajurit/anggota {$genderLabel} (maksimal {$maxDays} hari kerja). Pelaksanaan cuti menyesuaikan kebijakan atasan.",
                ]);
            }
        } elseif ($leaveType->default_days > 0 && $workingDays > $leaveType->default_days) {
            return back()->withInput()->withErrors([
                'start_date' => "Jumlah hari kerja yang diajukan ({$workingDays} hari) melebihi batas maksimal untuk {$leaveType->name} ({$leaveType->default_days} hari).",
            ]);
        }

        // Check overlapping leave requests
        $overlap = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_date' => 'Anda sudah memiliki pengajuan cuti lain pada rentang tanggal tersebut.',
            ]);
        }

        $requestNumber = 'CUTI/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -5));

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
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
            $filename = time() . '_permohonan_' . $file->getClientOriginalName();
            $path = $file->storeAs('leave_documents', $filename, 'public');

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
                $filename = time() . '_pendukung_' . $file->getClientOriginalName();
                $path = $file->storeAs('leave_documents', $filename, 'public');

                LeaveRequestDocument::create([
                    'leave_request_id' => $leaveRequest->id,
                    'document_name' => '[Lampiran Pendukung] ' . $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        \App\Helpers\ActivityLogger::log('Pengajuan Cuti', "Mengajukan cuti baru ({$leaveType->name}) mulai " . \Carbon\Carbon::parse($request->start_date)->format('d M Y'));

        return redirect()->route('user.leave.show', $leaveRequest->id)
            ->with('success', 'Pengajuan cuti berhasil dikirim! Menunggu verifikasi dari Staf Personalia.');
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

        if (!Storage::disk('public')->exists($letter->file_path)) {
            abort(404, 'File Surat Cuti Resmi tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($letter->file_path);
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
}
