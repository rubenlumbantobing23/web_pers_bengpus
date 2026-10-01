<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personel;
use App\Models\LeaveRequest;
use App\Models\MarriageApplication;
use App\Models\ActivityLog;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. STATISTIK UTAMA (General Monitoring)
        $totalPersonel = Personel::where('status_aktif', true)->count();
        
        $totalLeave = LeaveRequest::count();
        $totalMarriage = MarriageApplication::count();
        $totalPengajuan = $totalLeave + $totalMarriage;
        
        $pendingLeave = LeaveRequest::where('status', 'pending')->count();
        $pendingMarriage = MarriageApplication::whereIn('status', ['DIAJUKAN', 'PENGAJUAN_DISETUJUI', 'PERLU_PERBAIKAN', 'DIVERIFIKASI'])->count();
        $totalPending = $pendingLeave + $pendingMarriage;

        $recentActivityCount = ActivityLog::where('created_at', '>=', now()->subDays(7))->count();

        // 2. MONITORING LAYANAN (Detail Module Stats)
        $leaveStats = [
            'total' => $totalLeave,
            'pending' => $pendingLeave,
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        $marriageStats = [
            'total' => $totalMarriage,
            'pending' => $pendingMarriage,
            'approved' => MarriageApplication::whereIn('status', ['DISETUJUI', 'SELESAI'])->count(),
            'rejected' => MarriageApplication::where('status', 'DITOLAK')->count(),
        ];

        // 3. AKTIVITAS TERBARU (General Activity)
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalPersonel',
            'totalPengajuan',
            'totalPending',
            'recentActivityCount',
            'leaveStats',
            'marriageStats',
            'recentActivities'
        ));
    }
}
