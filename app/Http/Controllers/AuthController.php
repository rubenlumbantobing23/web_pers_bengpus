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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            \App\Helpers\ActivityLogger::log('Login', 'Berhasil login ke dalam sistem.');

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang di Dashboard Admin Personalia.');
            }

            return redirect()->intended(route('user.dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
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
                'message' => 'Data NRP/NIP tidak ditemukan dalam data nominatif personel. Silakan hubungi staf personalia.',
            ]);
        }

        if ($personel->user_id !== null) {
            return response()->json([
                'found' => false,
                'has_account' => true,
                'message' => 'Personel ini sudah memiliki akun terdaftar. Silakan gunakan akun yang sudah terdaftar atau hubungi staf personalia jika mengalami kendala.',
            ]);
        }

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
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nrp_nip' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'no_hp' => ['nullable', 'string', 'max:50'],
            'organization_unit_id' => ['nullable', 'exists:organization_units,id'],
        ]);

        $nrpNipClean = trim($request->nrp_nip);
        $personel = Personel::where('nrp_nip', $nrpNipClean)->first();

        if (!$personel) {
            return back()->withInput()->withErrors([
                'nrp_nip' => 'Data NRP/NIP tidak ditemukan dalam data nominatif personel. Silakan hubungi staf personalia.',
            ]);
        }

        if ($personel->user_id !== null) {
            return back()->withInput()->withErrors([
                'nrp_nip' => 'Personel ini sudah memiliki akun terdaftar. Silakan gunakan akun yang sudah terdaftar atau hubungi staf personalia jika mengalami kendala.',
            ]);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($personel, $request) {
            $user = User::create([
                'name' => $personel->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
            ]);

            $personel->update([
                'user_id' => $user->id,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'organization_unit_id' => $request->organization_unit_id,
            ]);

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
