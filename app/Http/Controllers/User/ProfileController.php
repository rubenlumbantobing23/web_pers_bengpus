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

        return view('user.profile', compact('user', 'personel'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $personel = $user->personel;

        $request->validate([
            'name' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|in:Pria,Wanita',
            'kategori_personel' => 'required|string|in:Perwira Menengah,Perwira Pertama,Bintara,Tamtama,PNS',
            'pangkat_golongan' => 'required|string|max:100',
            'jabatan' => 'required|string|max:150',
            'satuan_bagian' => 'required|string|max:150',
            'no_hp' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($personel) {
            $personel->update([
                'nama' => $request->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'kategori_personel' => $request->kategori_personel,
                'pangkat_golongan' => $request->pangkat_golongan,
                'jabatan' => $request->jabatan,
                'satuan_bagian' => $request->satuan_bagian,
                'no_hp' => $request->no_hp,
            ]);
        }

        return redirect()->route('user.profile')
            ->with('success', 'Profil Anda telah berhasil diperbarui.');
    }
}
