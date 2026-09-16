<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\InternalLetter;

class AdminLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = InternalLetter::with('uploader')->orderBy('letter_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $letters = $query->paginate(12)->withQueryString();

        $categories = [
            'Surat Cuti',
            'Surat Nikah',
            'Surat Perintah (Sprit)',
            'Surat Edaran Pers',
            'Nota Dinas',
            'Lain-lain',
        ];

        return view('admin.letters.index', compact('letters', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_number' => 'required|string|max:100',
            'letter_date' => 'required|date',
            'category' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
        $path = $file->storeAs('internal_letters', $filename, 'public');

        InternalLetter::create([
            'letter_number' => $request->letter_number,
            'letter_date' => $request->letter_date,
            'category' => $request->category,
            'subject' => $request->subject,
            'description' => $request->description,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'uploader_id' => Auth::id(),
        ]);

        return redirect()->route('admin.letters.index')
            ->with('success', 'Dokumen surat intern berhasil diarsip.');
    }

    public function download($id)
    {
        $letter = InternalLetter::findOrFail($id);

        if (!Storage::disk('public')->exists($letter->file_path)) {
            return back()->with('error', 'File surat tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->download($letter->file_path, $letter->letter_number . '_' . $letter->subject . '.' . pathinfo($letter->file_path, PATHINFO_EXTENSION));
    }

    public function destroy($id)
    {
        $letter = InternalLetter::findOrFail($id);

        if (Storage::disk('public')->exists($letter->file_path)) {
            Storage::disk('public')->delete($letter->file_path);
        }

        $letter->delete();

        return redirect()->route('admin.letters.index')
            ->with('success', 'Arsip surat intern berhasil dihapus.');
    }
}
