<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Helpers\LeaveCalculator;

use App\Models\Personel;

class AdminUserController extends Controller
{
    /**
     * Daftar pengguna web (akun anggota yang terdaftar di sistem)
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user')
            ->has('personel')
            ->with(['personel', 'leaveRequests', 'marriageRequests'])
            ->withCount(['leaveRequests', 'marriageRequests'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('personel', function ($pq) use ($search) {
                        $pq->where('nrp_nip', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $totalUsers = User::where('role', 'user')->has('personel')->count();
        $totalMiliter = User::where('role', 'user')->whereHas('personel', function($q) { $q->where('jenis_personel', 'militer'); })->count();
        $totalPns = User::where('role', 'user')->whereHas('personel', function($q) { $q->where('jenis_personel', 'pns'); })->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalMiliter', 'totalPns'));
    }

    /**
     * Detail akun pengguna web
     */
    public function show($id)
    {
        $user = User::where('role', 'user')
            ->with([
                'personel',
                'leaveRequests.leaveType',
                'marriageRequests',
                'leaveEntitlements',
            ])
            ->findOrFail($id);

        $entitlement = LeaveCalculator::getUserEntitlementSummary($user->id);

        return view('admin.users.show', compact('user', 'entitlement'));
    }


}
