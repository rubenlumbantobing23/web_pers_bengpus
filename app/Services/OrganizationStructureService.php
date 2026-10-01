<?php

namespace App\Services;

use App\Models\OrganizationOfficialAssignment;
use App\Models\OrganizationUnit;
use App\Models\Personel;
use Carbon\Carbon;

class OrganizationStructureService
{
    /**
     * Get the active official assignment for a specific unit and role.
     *
     * @param int $unitId
     * @param string $role
     * @return OrganizationOfficialAssignment|null
     */
    public function getActiveOfficialForUnit($unitId, $role)
    {
        return OrganizationOfficialAssignment::where('organization_unit_id', $unitId)
            ->where('role', $role)
            ->where('is_active', true)
            ->with('personel')
            ->first();
    }

    /**
     * Get active official assignment by role across all units.
     *
     * @param string $role
     * @return OrganizationOfficialAssignment|null
     */
    public function getActiveOfficialByRole(string $role): ?OrganizationOfficialAssignment
    {
        return OrganizationOfficialAssignment::where('role', $role)
            ->where('is_active', true)
            ->with('personel')
            ->latest('valid_from')
            ->first();
    }

    /**
     * Get Personel for a signer role (kabeng, wakabeng/waka, kabagum, kabag).
     * Sesuai request: data terintegrasi ke seluruh modul dan surat-suratan dari struktur organisasi.
     *
     * @param string $role
     * @return Personel|null
     */
    public function getSignerPersonel(string $role): ?Personel
    {
        $normalizedRole = strtolower(trim($role));
        if (in_array($normalizedRole, ['waka', 'wakil kepala', 'wakil_kepala', 'wakil'])) {
            $normalizedRole = 'wakabeng';
        }
        if (in_array($normalizedRole, ['kepala', 'kabengpuskomlek', 'ka'])) {
            $normalizedRole = 'kabeng';
        }

        // 1. Prioritas: Cari dari assignment aktif berdasarkan role
        $assignment = $this->getActiveOfficialByRole($normalizedRole);
        if (!$assignment && $normalizedRole === 'kabeng') {
            $assignment = $this->getActiveOfficialByRole('kepala');
        } elseif (!$assignment && $normalizedRole === 'wakabeng') {
            $assignment = $this->getActiveOfficialByRole('wakil_kepala') ?? $this->getActiveOfficialByRole('wakil kepala');
        }

        if ($assignment && $assignment->personel) {
            return $assignment->personel;
        }

        // 2. Cari dari unit dengan nama/kode yang bersesuaian
        $searchNames = [$normalizedRole];
        if ($normalizedRole === 'kabeng') {
            $searchNames = ['KABENG', 'KEPALA', 'KEPALA BENGPUSKOMLEK'];
        } elseif ($normalizedRole === 'wakabeng') {
            $searchNames = ['WAKABENG', 'WAKIL KEPALA', 'WAKA'];
        } elseif ($normalizedRole === 'kabagum') {
            $searchNames = ['KABAGUM', 'BAGUM'];
        } elseif ($normalizedRole === 'kabagrendal') {
            $searchNames = ['KABAGRENDAL', 'BAGRENDAL'];
        } elseif ($normalizedRole === 'pasipers') {
            $searchNames = ['PASIPERS', 'SIPERS'];
        }

        $unit = OrganizationUnit::where(function($q) use ($searchNames) {
            foreach ($searchNames as $s) {
                $q->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            }
        })->where('is_active', true)->first();

        if ($unit) {
            $unitAssignment = OrganizationOfficialAssignment::where('organization_unit_id', $unit->id)
                ->where('is_active', true)
                ->with('personel')
                ->first();
            if ($unitAssignment && $unitAssignment->personel) {
                return $unitAssignment->personel;
            }
        }
        return null;
    }

    /**
     * Get Supervisor Personel dynamically based on the Personel's organization_unit_id.
     * Searches upwards in the organization hierarchy until an active official is found.
     *
     * @param Personel|null $personel
     * @return Personel|null
     */
    public function getSupervisorForPersonel(?Personel $personel): ?Personel
    {
        if (!$personel) {
            return null;
        }

        if (!$personel->organization_unit_id) {
            $fallbackKabagum = $this->getSignerPersonel('kabagum');
            if ($fallbackKabagum && $fallbackKabagum->id !== $personel->id) {
                return $fallbackKabagum;
            }
            $fallbackKabeng = $this->getSignerPersonel('kabeng');
            if ($fallbackKabeng && $fallbackKabeng->id !== $personel->id) {
                return $fallbackKabeng;
            }
            return null;
        }

        $currentUnitId = $personel->organization_unit_id;

        while ($currentUnitId) {
            $unit = OrganizationUnit::find($currentUnitId);
            if (!$unit) {
                break;
            }

            // Check if there is an active official for this unit
            $assignment = OrganizationOfficialAssignment::where('organization_unit_id', $unit->id)
                ->where('is_active', true)
                ->with('personel')
                ->first();

            if ($assignment && $assignment->personel) {
                if ($assignment->personel_id !== $personel->id) {
                    return $assignment->personel;
                }
            }

            // If not found or if the official is themselves, go up to the parent unit
            $currentUnitId = $unit->parent_id;
        }

        // Fallback if no supervisor is found in the hierarchy
        $fallbackKabagum = $this->getSignerPersonel('kabagum');
        if ($fallbackKabagum && $fallbackKabagum->id !== $personel->id) {
            return $fallbackKabagum;
        }
        $fallbackKabeng = $this->getSignerPersonel('kabeng');
        if ($fallbackKabeng && $fallbackKabeng->id !== $personel->id) {
            return $fallbackKabeng;
        }

        return null;
    }

    /** Resolve the nearest active Kabag assignment linked to the person's unit hierarchy. */
    public function getKabagForPersonel(?Personel $personel): ?Personel
    {
        if (!$personel) {
            return null;
        }

        $unitId = $personel->organization_unit_id;
        if (!$unitId && $personel->satuan_bagian) {
            $unitId = OrganizationUnit::where('is_active', true)
                ->where(function ($query) use ($personel) {
                    $query->where('name', trim($personel->satuan_bagian))
                        ->orWhere('code', trim($personel->satuan_bagian));
                })
                ->value('id');
        }

        if (!$unitId) {
            return null;
        }

        $visited = [];

        while ($unitId && !in_array($unitId, $visited, true)) {
            $visited[] = $unitId;

            $assignments = OrganizationOfficialAssignment::where('organization_unit_id', $unitId)
                ->whereIn('role', ['kabag', 'kabagum', 'kabagrendal'])
                ->where('is_active', true)
                ->with('personel')
                ->latest('valid_from')
                ->get();

            $assignment = $assignments->sortBy(fn ($item) => match ($item->role) {
                'kabag' => 0,
                'kabagum' => 1,
                'kabagrendal' => 2,
                default => 3,
            })->first(fn ($item) => $item->personel && $item->personel_id !== $personel->id);

            if ($assignment) {
                return $assignment->personel;
            }

            $unitId = OrganizationUnit::whereKey($unitId)->value('parent_id');
        }

        return null;
    }

    /**
     * Get Personel atasan langsung untuk satuan / bagian tertentu.
     *
     * @param string|null $satuan
     * @return Personel|null
     */
    public function getSupervisorPersonelForSatuan(?string $satuan): ?Personel
    {
        if (empty($satuan)) {
            return $this->getSignerPersonel('kabagum') ?? $this->getSignerPersonel('kabeng');
        }

        $cleanSatuan = trim($satuan);

        // 1. Prioritas: Cari unit di struktur organisasi yang cocok dengan nama / kode satuan
        $unit = OrganizationUnit::where(function ($q) use ($cleanSatuan) {
            $q->where('name', $cleanSatuan)
              ->orWhere('code', $cleanSatuan)
              ->orWhere('name', 'like', "%{$cleanSatuan}%");
        })->where('is_active', true)->first();

        if ($unit) {
            // Cek pejabat aktif pada unit tersebut
            $assignment = OrganizationOfficialAssignment::where('organization_unit_id', $unit->id)
                ->where('is_active', true)
                ->with('personel')
                ->first();

            if ($assignment && $assignment->personel) {
                return $assignment->personel;
            }

            // Jika unit anak belum memiliki pejabat, naik ke unit induk (parent)
            if ($unit->parent_id) {
                $parentAssignment = OrganizationOfficialAssignment::where('organization_unit_id', $unit->parent_id)
                    ->where('is_active', true)
                    ->with('personel')
                    ->first();

                if ($parentAssignment && $parentAssignment->personel) {
                    return $parentAssignment->personel;
                }
            }
        }

        // 2. Fallback: Ambil Kabagum atau Kabeng pusat
        return $this->getSignerPersonel('kabagum') ?? $this->getSignerPersonel('kabeng');
    }

    /**
     * Assign a new official to a unit, soft-deleting the old one if it exists.
     *
     * @param int $unitId
     * @param int $personelId
     * @param string $role
     * @return OrganizationOfficialAssignment
     */
    public function assignOfficial($unitId, $personelId, $role)
    {
        // Find existing active official for this role and unit
        $existing = $this->getActiveOfficialForUnit($unitId, $role);

        if ($existing) {
            // If it's the exact same person, no need to update
            if ($existing->personel_id == $personelId) {
                return $existing;
            }

            // Deactivate the old assignment
            $existing->update([
                'is_active' => false,
                'valid_until' => Carbon::now(),
            ]);
        }

        // Create new assignment
        $newAssignment = OrganizationOfficialAssignment::create([
            'organization_unit_id' => $unitId,
            'personel_id' => $personelId,
            'role' => $role,
            'is_active' => true,
            'valid_from' => Carbon::now(),
        ]);

        return $newAssignment;
    }

    /**
     * Deactivate an official assignment.
     *
     * @param int $assignmentId
     * @return bool
     */
    public function deactivateAssignment($assignmentId)
    {
        $assignment = OrganizationOfficialAssignment::find($assignmentId);
        if ($assignment && $assignment->is_active) {
            $assignment->update([
                'is_active' => false,
                'valid_until' => Carbon::now(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Resolve corps for a Personel or NRP string based on nominatif personil.
     * Mengambil data corps dari nominatif personil (tabel personels via nrp/nip).
     * Jika personil militer Bengpuskomlek dan corps belum terisi di nominatif,
     * fallback ke 'Cke'. Untuk PNS, return empty string ('').
     *
     * @param Personel|string|null $personelOrNrp
     * @return string
     */
    public function resolveCorpsForPersonel($personelOrNrp): string
    {
        $personel = null;
        if ($personelOrNrp instanceof Personel) {
            $personel = $personelOrNrp;
        } elseif (is_string($personelOrNrp) && trim($personelOrNrp) !== '' && trim($personelOrNrp) !== '-') {
            $nrp = trim($personelOrNrp);
            $personel = Personel::where('nrp_nip', $nrp)->first();
        }

        if ($personel) {
            $corps = trim($personel->corps ?? '');
            if ($corps !== '' && $corps !== '-' && strtolower($corps) !== 'null') {
                return $corps;
            }

            // Jika jenis_personel adalah PNS, tidak memiliki corps militer
            if (isset($personel->jenis_personel) && strtolower($personel->jenis_personel) === 'pns') {
                return '';
            }

            // Jika pangkat golongan PNS (Golongan I, II, III, IV)
            if (isset($personel->pangkat_golongan) && preg_match('/^(I|II|III|IV)\//i', trim($personel->pangkat_golongan))) {
                return '';
            }

            // Default corps perhubungan untuk personil militer di lingkungan Bengpuskomlekad
            return 'Cke';
        }

        // Jika hanya berupa string NRP tanpa record Personel di database
        if (is_string($personelOrNrp)) {
            $trimmed = trim($personelOrNrp);
            // NIP PNS biasanya 18 digit (misal 19710916...)
            if (preg_match('/^19\d{16}$/', $trimmed)) {
                return '';
            }
            if ($trimmed !== '' && $trimmed !== '-') {
                return 'Cke';
            }
        }

        return '';
    }
}
