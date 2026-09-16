<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MarriageRequest;

class AdminMarriageController extends Controller
{
    public function index(Request $request)
    {
        $query = MarriageRequest::with(['user.personel'])
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

        $marriageRequests = $query->paginate(15)->withQueryString();

        return view('admin.marriage.index', compact('marriageRequests'));
    }

    public function show($id)
    {
        $marriageRequest = MarriageRequest::with(['user.personel', 'documents', 'approvedBy'])
            ->findOrFail($id);

        return view('admin.marriage.show', compact('marriageRequest'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika pengajuan ditolak.',
        ]);

        $marriageRequest = MarriageRequest::findOrFail($id);

        $marriageRequest->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $statusLabel = $request->status === 'approved' ? 'disetujui' : ($request->status === 'rejected' ? 'ditolak' : 'dibatalkan');

        return redirect()->route('admin.marriage.show', $id)
            ->with('success', "Status pengajuan nikah {$marriageRequest->request_number} berhasil diperbarui menjadi {$statusLabel}.");
    }
}
