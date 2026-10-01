<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Personel;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $personel = $user->personel;

        $structureService = app(\App\Services\OrganizationStructureService::class);
        $supervisor = $structureService->getSupervisorForPersonel($personel);

        $officials = \App\Models\OrganizationOfficialAssignment::where('is_active', true)
            ->with(['personel', 'unit'])
            ->get()
            ->sortBy(function($o) { return $o->unit ? $o->unit->sort_order : 999; });

        $isPejabat = \App\Models\OrganizationOfficialAssignment::where('personel_id', $personel->id)
            ->where('is_active', true)
            ->exists();

        return view('user.profile', compact('user', 'personel', 'supervisor', 'officials', 'isPejabat'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $personel = $user->personel;

        $isPejabat = \App\Models\OrganizationOfficialAssignment::where('personel_id', $personel->id)
            ->where('is_active', true)
            ->exists();

        $rules = [
            'no_hp' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ];

        if (!$isPejabat) {
            $rules['organization_unit_id'] = 'required|exists:organization_units,id';
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

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if ($personel) {
            $updateData = ['no_hp' => $request->no_hp];
            if (!$isPejabat) {
                $updateData['organization_unit_id'] = $request->organization_unit_id;
            }
            $personel->update($updateData);
        }

        return redirect()->route('user.profile')
            ->with('success', 'Profil Anda telah berhasil diperbarui.');
    }
}
