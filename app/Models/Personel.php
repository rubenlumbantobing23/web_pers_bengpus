<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Personel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_personel',
        'kategori_personel',
        'nrp_nip',
        'nama',
        'pangkat_golongan',
        'jabatan',
        'satuan_bagian',
        'organization_unit_id',
        'no_hp',
        'email',
        'status_aktif',
        // Kepangkatan
        'tmt_pangkat',
        'corps',
        'tmt_jabatan',
        'tmt_tni_pa',
        'mkg',
        // Identitas
        'agama_suku',
        'tgl_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'status_pernikahan',
        // Pendidikan
        'dikum_ti',
        'thn_lulus_dikum',
        'dik_pertama_tni',
        'thn_lulus_dik_pertama',
        'dikmit_tni',
        'thn_lulus_dikmit',
        'pendidikan_lanjutan',
        'tahun_lulus_lanjutan',
        // Keterangan
        'ket',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'tgl_lahir'    => 'date',
    ];

    // ─── Relasi ─────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    // ─── Scopes ─────────────────────────────────────────
    public function scopeMiliter(Builder $query): Builder
    {
        return $query->where('jenis_personel', 'militer');
    }

    public function scopePns(Builder $query): Builder
    {
        return $query->where('jenis_personel', 'pns');
    }

    public function scopePerwira(Builder $query): Builder
    {
        return $query->where('status_aktif', true)
            ->where(function ($q) {
                $q->where('kategori_personel', 'like', '%Perwira%')
                  ->orWhere(function ($sub) {
                      $sub->where(function ($rank) {
                          $rank->where('pangkat_golongan', 'like', '%Jenderal%')
                               ->orWhere('pangkat_golongan', 'like', '%Letjen%')
                               ->orWhere('pangkat_golongan', 'like', '%Mayjen%')
                               ->orWhere('pangkat_golongan', 'like', '%Brigjen%')
                               ->orWhere('pangkat_golongan', 'like', '%Kolonel%')
                               ->orWhere('pangkat_golongan', 'like', '%Letkol%')
                               ->orWhere('pangkat_golongan', 'like', '%Mayor%')
                               ->orWhere('pangkat_golongan', 'like', '%mayor%')
                               ->orWhere('pangkat_golongan', 'like', '%Kapten%')
                               ->orWhere('pangkat_golongan', 'like', '%Lettu%')
                               ->orWhere('pangkat_golongan', 'like', '%Letda%');
                      })
                      ->where('pangkat_golongan', 'not like', '%Sersan%')
                      ->where('pangkat_golongan', 'not like', '%Serma%')
                      ->where('pangkat_golongan', 'not like', '%Serka%')
                      ->where('pangkat_golongan', 'not like', '%Sertu%')
                      ->where('pangkat_golongan', 'not like', '%Serda%');
                  });
            });
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama',               'like', "%{$keyword}%")
              ->orWhere('nrp_nip',          'like', "%{$keyword}%")
              ->orWhere('pangkat_golongan',  'like', "%{$keyword}%")
              ->orWhere('jabatan',           'like', "%{$keyword}%")
              ->orWhere('satuan_bagian',     'like', "%{$keyword}%")
              ->orWhere('corps',             'like', "%{$keyword}%");
        });
    }

    // ─── Helpers ────────────────────────────────────────
    public function getLabelNomorIndukAttribute(): string
    {
        return $this->jenis_personel === 'pns' ? 'NIP' : 'NRP';
    }

    /** Baris pertama dari agama_suku = Agama */
    public function getAgamaAttribute(): ?string
    {
        if (!$this->agama_suku) return null;
        $parts = preg_split('/[\n\/\|]+/', $this->agama_suku, 2);
        return trim($parts[0]) ?: null;
    }

    /** Baris kedua dari agama_suku = Suku */
    public function getSukuAttribute(): ?string
    {
        if (!$this->agama_suku) return null;
        $parts = preg_split('/[\n\/\|]+/', $this->agama_suku, 2);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }
}

