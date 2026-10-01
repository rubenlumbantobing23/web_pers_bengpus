<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrganizationUnit;
use App\Models\OrganizationOfficialAssignment;
use App\Models\Personel;
use App\Services\OrganizationStructureService;

class OrganizationStructureController extends Controller
{
    protected $structureService;

    public function __construct(OrganizationStructureService $structureService)
    {
        $this->structureService = $structureService;
    }

    public function index()
    {
        // Get units for tree view — load root units with 3 levels of children (parent_id based)
        $units = OrganizationUnit::whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'assignments' => fn($q) => $q->where('is_active', true)->with('personel'),
                'children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')->with([
                    'assignments' => fn($aq) => $aq->where('is_active', true)->with('personel'),
                    'children' => fn($cq) => $cq->where('is_active', true)->orderBy('sort_order')->with([
                        'assignments' => fn($caq) => $caq->where('is_active', true)->with('personel'),
                        'children' => fn($ccq) => $ccq->where('is_active', true)->orderBy('sort_order')->with([
                            'assignments' => fn($ccaq) => $ccaq->where('is_active', true)->with('personel'),
                        ]),
                    ]),
                ]),
            ])
            ->orderBy('sort_order')
            ->get();
            
        // Get all active units for parent dropdowns
        $allUnits = OrganizationUnit::where('is_active', true)->orderBy('name')->get();

        // Get assignable units (excludes category grouping titles)
        $categoryHeaders = ['UNSUR PIMPINAN', 'KELOMPOK PIMPINAN', 'UNSUR PEMBANTU PIMPINAN', 'UNSUR PELAYANAN', 'UNSUR PELAKSANA'];
        $assignableUnits = OrganizationUnit::where('is_active', true)
            ->whereNotIn('name', $categoryHeaders)
            ->orderBy('name')
            ->get();

        // Get personels for dropdown (hanya perwira ke atas)
        $personels = Personel::perwira()->orderBy('nama')->get();

        // Get history of assignments
        $history = OrganizationOfficialAssignment::with(['unit', 'personel'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.organization.index', compact('units', 'allUnits', 'assignableUnits', 'personels', 'history'));
    }

    public function storeUnit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:organization_units,id',
            'level' => 'required|in:unit,subunit',
            'sort_order' => 'nullable|integer',
        ]);

        OrganizationUnit::create([
            'name' => $request->name,
            'code' => $request->code,
            'parent_id' => $request->parent_id,
            'level' => $request->level,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.organization.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function updateUnit(Request $request, OrganizationUnit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:organization_units,id',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $unit->update([
            'name' => $request->name,
            'code' => $request->code,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.organization.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function assignOfficial(Request $request)
    {
        $request->validate([
            'organization_unit_id' => 'required|exists:organization_units,id',
            'personel_id' => 'required|exists:personels,id',
            'role' => 'required|string|max:255',
        ]);

        $unit = OrganizationUnit::findOrFail($request->organization_unit_id);
        if ($unit->isCategoryHeader()) {
            return redirect()->route('admin.organization.index')
                ->with('error', 'Unit "' . $unit->name . '" merupakan judul kelompok dan tidak memiliki pejabat.');
        }

        $this->structureService->assignOfficial(
            $request->organization_unit_id,
            $request->personel_id,
            $request->role
        );

        return redirect()->route('admin.organization.index')->with('success', 'Pejabat berhasil ditugaskan.');
    }

    public function deactivateAssignment(OrganizationOfficialAssignment $assignment)
    {
        $this->structureService->deactivateAssignment($assignment->id);
        return redirect()->route('admin.organization.index')->with('success', 'Penugasan pejabat berhasil dinonaktifkan.');
    }
}
