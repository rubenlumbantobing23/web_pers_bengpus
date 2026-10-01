<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarriagePartner extends Model
{
    protected $fillable = [
        'marriage_application_id', 'peran', 'status_pernikahan', 'nama', 'tempat_lahir', 'tanggal_lahir',
        'pekerjaan', 'status_pekerjaan', 'instansi', 'jabatan', 'agama', 'suku',
        'alamat', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi',
        'bapak_nama', 'bapak_agama', 'bapak_pekerjaan', 'bapak_alamat',
        'ibu_nama', 'ibu_agama', 'ibu_pekerjaan', 'ibu_alamat'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(MarriageApplication::class, 'marriage_application_id');
    }
}
