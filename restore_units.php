<?php
$p = App\Models\OrganizationUnit::where('name', 'UNSUR PIMPINAN')->first();
App\Models\OrganizationUnit::updateOrCreate(['name' => 'KEPALA'], ['parent_id' => $p->id, 'code' => 'KABENG', 'level' => 'subunit', 'sort_order' => 10, 'is_active' => true]);
App\Models\OrganizationUnit::updateOrCreate(['name' => 'WAKIL KEPALA'], ['parent_id' => $p->id, 'code' => 'WAKABENG', 'level' => 'subunit', 'sort_order' => 20, 'is_active' => true]);
$p2 = App\Models\OrganizationUnit::where('name', 'UNSUR PEMBANTU PIMPINAN')->first();
App\Models\OrganizationUnit::updateOrCreate(['name' => 'KABAGUM'], ['parent_id' => $p2->id, 'code' => 'KABAGUM', 'level' => 'subunit', 'sort_order' => 10, 'is_active' => true]);
App\Models\OrganizationUnit::updateOrCreate(['name' => 'KABAGRENDAL'], ['parent_id' => $p2->id, 'code' => 'KABAGRENDAL', 'level' => 'subunit', 'sort_order' => 20, 'is_active' => true]);
$p3 = App\Models\OrganizationUnit::where('name', 'UNSUR PELAYANAN')->first();
App\Models\OrganizationUnit::updateOrCreate(['name' => 'PASITUUD'], ['parent_id' => $p3->id, 'code' => 'PASITUUD', 'level' => 'subunit', 'sort_order' => 10, 'is_active' => true]);
echo "Units restored\n";
