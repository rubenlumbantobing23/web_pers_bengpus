<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationOfficialAssignment extends Model
{
    protected $fillable = [
        'organization_unit_id',
        'personel_id',
        'role',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function personel()
    {
        return $this->belongsTo(Personel::class, 'personel_id');
    }

    /**
     * Daftar sebutan peran / jabatan pejabat unit
     */
    public static function roleLabels(): array
    {
        return [
            'kepala' => 'Kepala',
            'wakil_kepala' => 'Wakil Kepala',
            'kabeng' => 'Kepala (Kabeng)',
            'wakabeng' => 'Wakil Kepala (Wakabeng)',
            'kabagum' => 'Kabagum',
            'kabagrendal' => 'Kabagrendal',
            'kabag' => 'Kabag',
            'pasipam' => 'Pasipam',
            'pasiops' => 'Pasiops',
            'pasipers' => 'Pasipers',
            'pasilog' => 'Pasilog',
            'pasirendal' => 'Pasirendal',
            'pasituud' => 'Pasituud',
            'kabengsiskom' => 'Kabengsiskom',
            'kabengsislek' => 'Kabengsislek',
            'kabengjaringan_tik' => 'Kabengjaringan & TIK',
            'kabengjaringan dan tik' => 'Kabengjaringan & TIK',
            'kabengintegrasi_power' => 'Kabengintegrasi & Power System',
            'kabengintegrasi dan power system' => 'Kabengintegrasi & Power System',
            'kasub' => 'Kasub / Kasubbeng',
            'kagud' => 'Kagud',
            'kaur' => 'Kaur',
            'paur' => 'Paur',
            'plh' => 'PLH',
            'pejabat_lain' => 'Pejabat Lain',
        ];
    }

    /**
     * Accessor untuk label peran yang rapi
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = self::roleLabels();
        $key = strtolower(trim($this->role ?? ''));
        return $labels[$key] ?? ($labels[$this->role] ?? strtoupper(str_replace('_', ' ', $this->role)));
    }
}

