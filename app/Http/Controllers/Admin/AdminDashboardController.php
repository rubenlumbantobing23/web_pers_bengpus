<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personel;
use App\Models\LeaveRequest;
use App\Models\MarriageRequest;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalPersonel = Personel::where('status_aktif', true)->count();
        $totalLeaveRequests = LeaveRequest::count();
        $pendingLeaveRequests = LeaveRequest::where('status', 'pending')->count();
        $approvedLeaveRequests = LeaveRequest::where('status', 'approved')->count();

        $totalMarriageRequests = MarriageRequest::count();
        $pendingMarriageRequests = MarriageRequest::where('status', 'pending')->count();

        // Recent requests combined or filtered
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

        $recentLeaveRequests = $query->paginate(10)->withQueryString();

        return view('admin.dashboard', compact(
            'totalPersonel',
            'totalLeaveRequests',
            'pendingLeaveRequests',
            'approvedLeaveRequests',
            'totalMarriageRequests',
            'pendingMarriageRequests',
            'recentLeaveRequests'
        ));
    }
}
