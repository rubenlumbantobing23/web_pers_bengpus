<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\InternalLetter;
use App\Models\LetterType;
use App\Models\ActivityLog;
use App\Models\SecretLetterSetting;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AdminLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = InternalLetter::with(['uploader', 'letterType'])->orderBy('letter_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sender', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%");
            });
        }

        if ($request->filled('direction')) {
            if ($request->direction !== 'Semua') {
                $query->where('direction', $request->direction);
            }
        }

        if ($request->filled('classification')) {
            if ($request->classification !== 'Semua') {
                $query->where('classification', $request->classification);
            }
        }

        if ($request->filled('letter_type_id')) {
            $query->where('letter_type_id', $request->letter_type_id);
        }

        $letters = $query->paginate(12)->withQueryString();
        
        $letterTypes = LetterType::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.letters.index', compact('letters', 'letterTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_number' => 'required|string|max:100',
            'letter_type_id' => 'required|exists:letter_types,id',
            'direction' => 'required|in:MASUK,KELUAR',
            'letter_date' => 'required|date',
            'received_date' => 'nullable|required_if:direction,MASUK|date',
            'sender' => 'nullable|required_if:direction,MASUK|string',
            'recipient' => 'nullable|required_if:direction,KELUAR|string',
            'classification' => 'required|in:BIASA,RAHASIA',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $letterType = LetterType::find($request->letter_type_id);
        $category = $letterType->name;

        $file = $request->file('file');
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
        
        if ($request->classification === 'RAHASIA') {
            $path = $file->storeAs('internal_letters_private', $filename, 'local');
        } else {
            $path = $file->storeAs('internal_letters', $filename, 'public');
        }

        InternalLetter::create([
            'letter_number' => $request->letter_number,
            'letter_date' => $request->letter_date,
            'category' => $category,
            'letter_type_id' => $request->letter_type_id,
            'direction' => $request->direction,
            'received_date' => $request->direction === 'MASUK' ? $request->received_date : null,
            'sender' => $request->direction === 'MASUK' ? $request->sender : null,
            'recipient' => $request->direction === 'KELUAR' ? $request->recipient : null,
            'classification' => $request->classification,
            'subject' => $request->subject,
            'description' => $request->description,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'uploader_id' => Auth::id(),
        ]);

        return redirect()->route('admin.letters.index')
            ->with('success', 'Dokumen surat intern berhasil diarsip.');
    }

    public function showSecretVerify($id)
    {
        $letter = InternalLetter::findOrFail($id);
        
        if ($letter->classification !== 'RAHASIA') {
            return redirect()->route('admin.letters.index');
        }

        return view('admin.letters.secret_verify', compact('letter'));
    }

    private function getOrCreateSecretLetterSetting()
    {
        $setting = SecretLetterSetting::first();
        if (!$setting) {
            $envPassword = env('SECRET_LETTER_PASSWORD');
            if (empty($envPassword)) {
                return null;
            }
            $setting = SecretLetterSetting::create([
                'password' => Hash::make($envPassword)
            ]);
        }
        return $setting;
    }

    public function verifySecret(Request $request, $id)
    {
        $letter = InternalLetter::findOrFail($id);
        
        if ($letter->classification !== 'RAHASIA') {
            return redirect()->route('admin.letters.index');
        }

        $setting = $this->getOrCreateSecretLetterSetting();
        
        if (!$setting) {
            return back()->with('error', 'Akses arsip rahasia belum dikonfigurasi oleh administrator.');
        }

        if (Hash::check($request->password, $setting->password)) {
            Session::put('secret_letter_verified_' . $letter->id, $setting->updated_at->timestamp);
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Akses Arsip Rahasia',
                'description' => 'Berhasil mengakses surat rahasia: ' . $letter->letter_number,
            ]);

            return redirect()->route('admin.letters.download', $letter->id);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Akses Arsip Rahasia Gagal',
            'description' => 'Gagal mengakses surat rahasia (password salah): ' . $letter->letter_number,
        ]);

        return back()->with('error', 'Password salah.');
    }

    public function showPasswordForm()
    {
        return view('admin.letters.password_settings');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $setting = $this->getOrCreateSecretLetterSetting();

        if (!$setting) {
            return back()->with('error', 'Sistem password belum terkonfigurasi. Harap isi SECRET_LETTER_PASSWORD di .env terlebih dahulu.');
        }

        if (!Hash::check($request->current_password, $setting->password)) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Perubahan Password Arsip Rahasia Gagal',
                'description' => 'Gagal mengganti password karena password saat ini salah.',
            ]);
            return back()->with('error', 'Password saat ini salah.');
        }

        $setting->update([
            'password' => Hash::make($request->new_password)
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Perubahan Password Arsip Rahasia',
            'description' => 'Berhasil mengganti password arsip rahasia.',
        ]);

        return back()->with('success', 'Password arsip rahasia berhasil diubah.');
    }

    public function download($id)
    {
        $letter = InternalLetter::findOrFail($id);

        if ($letter->classification === 'RAHASIA') {
            $setting = $this->getOrCreateSecretLetterSetting();
            $verifiedTimestamp = Session::get('secret_letter_verified_' . $letter->id);
            
            if (!$setting || $verifiedTimestamp !== $setting->updated_at->timestamp) {
                return redirect()->route('admin.letters.secret.verify_form', $letter->id);
            }
        }

        $disk = str_starts_with($letter->file_path, 'internal_letters_private') ? 'local' : 'public';

        if (!Storage::disk($disk)->exists($letter->file_path)) {
            return back()->with('error', 'File surat tidak ditemukan di penyimpanan server.');
        }

        $downloadName = preg_replace('/[^A-Za-z0-9\._-]/', '_', $letter->letter_number . '_' . $letter->subject) . '.' . pathinfo($letter->file_path, PATHINFO_EXTENSION);
        return Storage::disk($disk)->download($letter->file_path, $downloadName);
    }

    public function destroy($id)
    {
        $letter = InternalLetter::findOrFail($id);

        $disk = str_starts_with($letter->file_path, 'internal_letters_private') ? 'local' : 'public';

        if (Storage::disk($disk)->exists($letter->file_path)) {
            Storage::disk($disk)->delete($letter->file_path);
        }

        $letter->delete();

        return redirect()->route('admin.letters.index')
            ->with('success', 'Arsip surat intern berhasil dihapus.');
    }
}
