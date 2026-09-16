<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LeaveCalculator;
use App\Models\LeaveRequest;
use App\Models\MarriageRequest;
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

        $activeMarriageCount = MarriageRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $recentLeaveRequests = LeaveRequest::where('user_id', $user->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentMarriageRequests = MarriageRequest::where('user_id', $user->id)
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

        return view('user.dashboard', compact(
            'user',
            'entitlement',
            'activeLeaveCount',
            'activeMarriageCount',
            'recentLeaveRequests',
            'recentMarriageRequests',
            'holidays',
            'myLeaveDates'
        ));
    }
}
