<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationUnit extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'level',
        'is_active',
        'sort_order',
    ];

    public function parent()
    {
        return $this->belongsTo(OrganizationUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganizationUnit::class, 'parent_id');
    }

    public function assignments()
    {
        return $this->hasMany(OrganizationOfficialAssignment::class);
    }

    public function personels()
    {
        return $this->hasMany(Personel::class, 'organization_unit_id');
    }

    /**
     * Check if this unit is a grouping/category title (like KELOMPOK PIMPINAN, UNSUR PELAYANAN, UNSUR PELAKSANA)
     * which does not have direct assigned officials.
     */
    public function isCategoryHeader(): bool
    {
        $headers = [
            'UNSUR PIMPINAN', 
            'KELOMPOK PIMPINAN', 
            'UNSUR PEMBANTU PIMPINAN', 
            'UNSUR PELAYANAN', 
            'UNSUR PELAKSANA'
        ];
        return in_array(strtoupper(trim($this->name)), $headers);
    }
}
