<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveType;

class AdminLeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();

        return view('admin.leave_types.index', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'required_documents_info' => 'nullable|string',
            'default_days' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        LeaveType::create($request->all());

        return redirect()->route('admin.leave_types.index')
            ->with('success', 'Jenis cuti baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code,' . $id,
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'required_documents_info' => 'nullable|string',
            'default_days' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        $leaveType->update($request->all());

        return redirect()->route('admin.leave_types.index')
            ->with('success', 'Pengaturan jenis cuti berhasil diperbarui.');
    }
}
