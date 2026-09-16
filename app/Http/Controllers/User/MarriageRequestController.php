<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MarriageRequirement;
use App\Models\MarriageRequest;
use App\Models\MarriageRequestDocument;

class MarriageRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $marriageRequests = MarriageRequest::where('user_id', $user->id)
            ->with(['documents', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.marriage.index', compact('marriageRequests'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->personel && $user->personel->status_pernikahan === 'Menikah') {
            return redirect()->route('user.marriage.index')
                ->with('error', 'Anda sudah berstatus Menikah, tidak dapat mengajukan izin nikah baru.');
        }

        $requirements = MarriageRequirement::where('is_active', true)->get();

        return view('user.marriage.create', compact('requirements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'spouse_name' => 'required|string|max:255',
            'spouse_nrp_nip' => 'nullable|string|max:50',
            'spouse_occupation' => 'nullable|string|max:150',
            'marriage_date' => 'required|date|after_or_equal:today',
            'marriage_location' => 'required|string|max:255',
            'terms_accepted' => 'required|accepted',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'terms_accepted.accepted' => 'Anda harus menyetujui seluruh persyaratan permohonan izin nikah.',
            'marriage_date.after_or_equal' => 'Tanggal pernikahan tidak boleh di masa lalu.',
        ]);

        $user = Auth::user();

        if ($user->personel && $user->personel->status_pernikahan === 'Menikah') {
            return redirect()->route('user.marriage.index')
                ->with('error', 'Anda sudah berstatus Menikah, tidak dapat mengajukan izin nikah baru.');
        }

        $requestNumber = 'NKH/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -5));

        $marriageRequest = MarriageRequest::create([
            'user_id' => $user->id,
            'request_number' => $requestNumber,
            'spouse_name' => $request->spouse_name,
            'spouse_nrp_nip' => $request->spouse_nrp_nip,
            'spouse_occupation' => $request->spouse_occupation,
            'marriage_date' => $request->marriage_date,
            'marriage_location' => $request->marriage_location,
            'status' => 'pending',
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('marriage_documents', $filename, 'public');

                MarriageRequestDocument::create([
                    'marriage_request_id' => $marriageRequest->id,
                    'document_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('user.marriage.index')
            ->with('success', 'Pengajuan izin nikah berhasil dikirim! Menunggu verifikasi dari Staf Personalia.');
    }

    public function show($id)
    {
        $marriageRequest = MarriageRequest::where('user_id', Auth::id())
            ->with(['documents', 'approvedBy'])
            ->findOrFail($id);

        $requirements = MarriageRequirement::where('is_active', true)->get();

        return view('user.marriage.show', compact('marriageRequest', 'requirements'));
    }
}
