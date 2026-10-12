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

        $personelStats = [
            'militer' => Personel::where('jenis_personel', 'MILITER')->where('status_aktif', true)->count(),
            'pns' => Personel::where('jenis_personel', 'PNS')->where('status_aktif', true)->count(),
        ];

        return view('admin.dashboard', compact(
            'totalPersonel',
            'totalPengajuan',
            'totalPending',
            'leaveStats',
            'marriageStats',
            'personelStats'
        ));
    }

    public function readNotification($id)
    {
        $notification = \Illuminate\Support\Facades\Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $url = $notification->data['url'] ?? route('admin.dashboard');
        if (str_starts_with($url, 'http://localhost/')) {
            $url = str_replace('http://localhost/', '/', $url);
        }
        
        return redirect($url);
    }

    public function readAllNotifications()
    {
        \Illuminate\Support\Facades\Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}
