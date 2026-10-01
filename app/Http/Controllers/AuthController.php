<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Personel;
use App\Models\LeaveEntitlement;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }

        $totalPersonel = Personel::count();
        $activeLeaves = \App\Models\LeaveRequest::where('status', 'APPROVED')
                        ->where('start_date', '<=', now()->toDateString())
                        ->where('end_date', '>=', now()->toDateString())
                        ->count();

        return view('auth.login', compact('totalPersonel', 'activeLeaves'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginInput = $request->input('login_id');
        $password = $request->input('password');
        $remember = $request->boolean('remember');
        $userToLogin = null;

        // 1. Cari personels berdasarkan nrp_nip
        $personel = Personel::where('nrp_nip', $loginInput)->first();
        if ($personel && $personel->user_id) {
            $user = User::find($personel->user_id);
            if ($user && Hash::check($password, $user->password)) {
                if (!$personel->status_aktif) {
                    return back()->withErrors(['login_id' => 'Akun Personel Anda telah dinonaktifkan. Hubungi Admin.'])->onlyInput('login_id');
                }
                $userToLogin = $user;
            }
        }

        if ($userToLogin) {
            Auth::login($userToLogin, $remember);
            $request->session()->regenerate();

            \App\Helpers\ActivityLogger::log('Login', 'Berhasil login ke dalam sistem.');

            if ($userToLogin->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang di Dashboard Admin Personalia.');
            }

            return redirect()->route('user.dashboard')->with('success', 'Selamat datang kembali, ' . $userToLogin->name);
        }

        return back()->withErrors([
            'login_id' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('login_id');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('user.dashboard');
        }

        // Only show actual units, filter out jabatan/position entries
        // KASUBBENG work units (12-31) remain - personel are assigned there
        $officials = \App\Models\OrganizationOfficialAssignment::where('is_active', true)
            ->with(['personel', 'unit'])
            ->get()
            ->sortBy(function($o) { return $o->unit ? $o->unit->sort_order : 999; });

        return view('auth.register', compact('officials'));
    }

    public function checkNrp(Request $request)
    {
        $request->validate([
            'nrp_nip' => ['required', 'string'],
        ]);

        $nrpNipClean = trim($request->nrp_nip);
        $personel = Personel::where('nrp_nip', $nrpNipClean)->first();

        if (!$personel) {
            return response()->json([
                'found' => false,
                'message' => 'NRP/NIP belum terdaftar pada Nominatif Personel.',
            ]);
        }

        if (!$personel->status_aktif) {
            return response()->json([
                'found' => false,
                'message' => 'Personel tidak berstatus aktif dan tidak dapat melakukan registrasi.',
            ]);
        }

        if ($personel->user_id !== null) {
            return response()->json([
                'found' => false,
                'has_account' => true,
                'message' => 'NRP/NIP tersebut sudah memiliki akun.',
            ]);
        }

        $isPejabat = \App\Models\OrganizationOfficialAssignment::where('personel_id', $personel->id)
            ->where('is_active', true)
            ->exists();

        return response()->json([
            'found' => true,
            'personel' => [
                'nama' => $personel->nama,
                'nrp_nip' => $personel->nrp_nip,
                'pangkat_golongan' => $personel->pangkat_golongan,
                'jabatan' => $personel->jabatan,
                'satuan_bagian' => $personel->satuan_bagian ?? 'Bengpuskomlekad',
                'no_hp' => $personel->no_hp,
                'organization_unit_id' => $personel->organization_unit_id,
                'is_pejabat' => $isPejabat,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $nrpNipClean = trim($request->nrp_nip);
        $personel = Personel::where('nrp_nip', $nrpNipClean)->first();

        if (!$personel) {
            return back()->withInput()->withErrors([
                'nrp_nip' => 'NRP/NIP belum terdaftar pada Nominatif Personel.',
            ]);
        }

        if (!$personel->status_aktif) {
            return back()->withInput()->withErrors([
                'nrp_nip' => 'Personel tidak berstatus aktif dan tidak dapat melakukan registrasi.',
            ]);
        }

        if ($personel->user_id !== null) {
            return back()->withInput()->withErrors([
                'nrp_nip' => 'NRP/NIP tersebut sudah memiliki akun.',
            ]);
        }

        $isPejabat = \App\Models\OrganizationOfficialAssignment::where('personel_id', $personel->id)
            ->where('is_active', true)
            ->exists();

        $rules = [
            'nrp_nip' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:50'],
        ];

        if (!$isPejabat) {
            $rules['organization_unit_id'] = ['required', 'exists:organization_units,id'];
        }

        $request->validate($rules);

        if (!$isPejabat) {
            $validUnit = \App\Models\OrganizationOfficialAssignment::where('organization_unit_id', $request->organization_unit_id)
                ->where('is_active', true)
                ->exists();

            if (!$validUnit) {
                return back()->withInput()->withErrors([
                    'organization_unit_id' => 'Pilihan Atasan Langsung tidak valid atau pejabat tidak aktif.',
                ]);
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($personel, $request, $isPejabat) {
            $user = User::create([
                'name' => $personel->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
            ]);

            $updateData = [
                'user_id' => $user->id,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
            ];
            
            if (!$isPejabat) {
                $updateData['organization_unit_id'] = $request->organization_unit_id;
            }
            
            $personel->update($updateData);

            LeaveEntitlement::firstOrCreate([
                'user_id' => $user->id,
                'year' => date('Y'),
            ], [
                'total_days' => 12,
            ]);

            Auth::login($user);
            \App\Helpers\ActivityLogger::log('Registrasi', 'User mendaftar dan menghubungkan akun dengan data personel.', $user->id);
        });

        return redirect()->route('user.dashboard')->with('success', 'Registrasi berhasil! Akun Anda telah terhubung dengan data nominatif personel.');
    }

    public function logout(Request $request)
    {
        \App\Helpers\ActivityLogger::log('Logout', 'Keluar dari sistem.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
