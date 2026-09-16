<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;

class AdminHolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $holidays = Holiday::whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.holidays.index', compact('holidays', 'year'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:255',
            'type' => 'required|in:national_holiday,collective_leave',
            'description' => 'nullable|string',
        ]);

        $year = (int) substr($request->date, 0, 4);

        Holiday::create([
            'date' => $request->date,
            'name' => $request->name,
            'type' => $request->type,
            'year' => $year,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.holidays.index', ['year' => $year])
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $year = $holiday->year;
        $holiday->delete();

        return redirect()->route('admin.holidays.index', ['year' => $year])
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}
