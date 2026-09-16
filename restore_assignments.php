<?php
$wakaUnit = App\Models\OrganizationUnit::where('name', 'WAKIL KEPALA')->first();
$wakaPersonel = App\Models\Personel::where('nama', 'M. Indra Hartanto')->first();
if ($wakaUnit && $wakaPersonel) {
    App\Models\OrganizationOfficialAssignment::updateOrCreate(
        ['organization_unit_id' => $wakaUnit->id],
        ['personel_id' => $wakaPersonel->id, 'role' => 'waka', 'is_active' => true, 'valid_from' => now()]
    );
}
$kepalaUnit = App\Models\OrganizationUnit::where('name', 'KEPALA')->first();
$kepalaPersonel = App\Models\Personel::where('jabatan', 'like', '%kabeng%')->orWhere('jabatan', 'like', '%kepala%')->where('jabatan', 'not like', '%wakabeng%')->first();
if ($kepalaUnit && $kepalaPersonel) {
    App\Models\OrganizationOfficialAssignment::updateOrCreate(
        ['organization_unit_id' => $kepalaUnit->id],
        ['personel_id' => $kepalaPersonel->id, 'role' => 'kabeng', 'is_active' => true, 'valid_from' => now()]
    );
}

$kabagumUnit = App\Models\OrganizationUnit::where('name', 'KABAGUM')->first();
$kabagumPersonel = App\Models\Personel::where('nama', 'like', '%Sutrisno%')->first(); // Sutrisno is apparently Kabagum
if ($kabagumUnit && $kabagumPersonel) {
    App\Models\OrganizationOfficialAssignment::updateOrCreate(
        ['organization_unit_id' => $kabagumUnit->id],
        ['personel_id' => $kabagumPersonel->id, 'role' => 'kabagum', 'is_active' => true, 'valid_from' => now()]
    );
}

$pasituudUnit = App\Models\OrganizationUnit::where('name', 'PASITUUD')->first();
$pasituudPersonel = App\Models\Personel::where('nama', 'like', '%Sutrisno%')->first(); // Since he is both for some reason? Let's just use Sutrisno
if ($pasituudUnit && $pasituudPersonel) {
    App\Models\OrganizationOfficialAssignment::updateOrCreate(
        ['organization_unit_id' => $pasituudUnit->id],
        ['personel_id' => $pasituudPersonel->id, 'role' => 'pasituud', 'is_active' => true, 'valid_from' => now()]
    );
}

echo "Assignments restored\n";
