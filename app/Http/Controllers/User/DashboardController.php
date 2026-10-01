<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LeaveCalculator;
use App\Models\LeaveRequest;
use App\Models\MarriageApplication;
use App\Models\Holiday;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $entitlement = LeaveCalculator::getUserEntitlementSummary($user->id);

        $activeLeaveCount = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Updated to use MarriageApplication
        $activeMarriageCount = MarriageApplication::where('user_id', $user->id)
            ->whereIn('status', ['DIAJUKAN', 'PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN', 'DIVERIFIKASI'])
            ->count();

        $recentLeaveRequests = LeaveRequest::where('user_id', $user->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Updated to use MarriageApplication
        $recentMarriageRequests = MarriageApplication::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $holidays = Holiday::whereYear('date', date('Y'))
            ->orderBy('date', 'asc')
            ->get();

        // Get approved/pending leave dates for calendar rendering
        $myLeaveDates = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->get(['start_date', 'end_date', 'status', 'working_days_count']);

        // Calculate General Statistics
        $totalLeave = LeaveRequest::where('user_id', $user->id)->count();
        $totalMarriage = MarriageApplication::where('user_id', $user->id)->count();
        $totalRequests = $totalLeave + $totalMarriage;

        $approvedLeave = LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->count();
        $approvedMarriage = MarriageApplication::where('user_id', $user->id)->whereIn('status', ['DISETUJUI', 'SELESAI'])->count();
        $approvedRequests = $approvedLeave + $approvedMarriage;
        
        $totalActive = $activeLeaveCount + $activeMarriageCount;
        $unreadNotifications = $user->unreadNotifications()->count();

        // Combine Activities for "Aktivitas Terbaru"
        $activities = collect();
        
        foreach($recentLeaveRequests as $req) {
            $activities->push([
                'type' => 'Cuti',
                'title' => 'Pengajuan ' . $req->leaveType->name,
                'status' => $req->status,
                'date' => $req->created_at,
                'url' => route('user.leave.show', $req->id)
            ]);
        }

        foreach($recentMarriageRequests as $req) {
            $activities->push([
                'type' => 'Nikah',
                'title' => 'Pengajuan Izin Nikah',
                'status' => strtolower(str_replace('_', ' ', $req->status)),
                'date' => $req->created_at,
                'url' => route('user.pengajuan_nikah.show', $req->id)
            ]);
        }

        $activities = $activities->sortByDesc('date')->take(5);

        return view('user.dashboard', compact(
            'user',
            'entitlement',
            'activeLeaveCount',
            'activeMarriageCount',
            'recentLeaveRequests',
            'recentMarriageRequests',
            'holidays',
            'myLeaveDates',
            'totalRequests',
            'approvedRequests',
            'totalActive',
            'unreadNotifications',
            'activities'
        ));
    }
}
