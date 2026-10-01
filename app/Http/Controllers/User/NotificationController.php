<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('user.notifications.index', compact('notifications'));
    }

    public function read($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        $url = route('user.notifications.index');

        // Gunakan route dinamis jika terdapat ID pengajuan agar link selalu mengikuti base URL yang aktif
        if (isset($notification->data['leave_request_id'])) {
            $url = route('user.leave.show', $notification->data['leave_request_id']);
        } elseif (isset($notification->data['url'])) {
            // Fallback untuk notifikasi lama atau modul lain
            $url = $notification->data['url'];
            
            // Hapus prefix localhost jika ada (hardcoded dari environment lama)
            if (str_starts_with($url, 'http://localhost') || str_starts_with($url, 'https://localhost')) {
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '/';
                
                // Jika path mengandung subfolder, coba bersihkan
                // Namun cara teraman adalah menggunakan relative path
                $url = url($path);
            }
        }

        return redirect($url);
    }

    public function readAll()
    {
        Auth::user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}